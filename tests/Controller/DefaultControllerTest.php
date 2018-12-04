<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;

class DefaultControllerTest extends WebTestCase
{
    public function testSendContactMessage()
    {
        $client = static::createClient();
        $client->enableProfiler();
        $client->followRedirects();

        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame('en', $client->getRequest()->getLocale());

        $crawler = $client->click($crawler->selectLink('Contact us')->link());

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame('Leave us a Message', $crawler->filter('#content h2:first-child')->text());

        $crawler = $client->submit($crawler->selectButton('Send my message')->form([
            'contact' => [
                'sender' => 'john',
                'subject' => 'Hello!',
                'message' => '',
            ],
        ]));

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(3, $crawler->filter('form[name="contact"] > div.has-error')->count());
        $this->assertContains(' This value is not a valid email address.', $crawler->filter('.has-error')->first()->text());
        $this->assertContains(' This value is too short. It should have 10 characters or more.', $crawler->filter('.has-error')->eq(1)->text());
        $this->assertContains(' You must type a message', $crawler->filter('.has-error')->last()->text());

        $client->followRedirects(false);

        $client->submit($crawler->selectButton('Send my message')->form([
            'contact' => [
                'sender' => 'john@oliver.news',
                'subject' => 'Can you send me more information about Symfony?',
                'message' => 'Hi everyone! I would like information about your Symfony training please.',
            ],
        ]));

        if ($profile = $client->getProfile()) {
            /** @var MessageDataCollector $collector */
            $collector = $profile->getCollector('swiftmailer');
            $this->assertSame(1, $collector->getMessageCount());

            /** @var \Swift_Message $message */
            $message = $collector->getMessages()[0];
            $this->assertSame(['john@oliver.news' => null], $message->getFrom());
            $this->assertSame(['training@sensiolabs.com' => null], $message->getTo());
            $this->assertSame('Can you send me more information about Symfony?', $message->getSubject());
            $this->assertSame('Hi everyone! I would like information about your Symfony training please.', $message->getBody());
        }

        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame('Your request has been successfully sent.', $crawler->filter('.alert-success')->text());
    }
}
