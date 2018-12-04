<?php

namespace App\Tests\Game;

use App\Game\Loader\TextFileLoader;
use App\Game\WordList;
use PHPUnit\Framework\TestCase;

class WordListTest extends TestCase
{
    /**
     * @expectedException App\Game\Exception\RuntimeException
     */
    public function testLoadDictionariesWithNoLoader()
    {
        $wordlist = new WordList(['/path/to/any/dictionary.txt']);
        $wordlist->getRandomWord();
    }

    public function testLoadDictionaries()
    {
        $randomWord = 'my-word-'.mt_rand();

        $loader = $this->getMockBuilder(TextFileLoader::class)->getMock();
        $loader->expects($this->once())
            ->method('load')
            ->will($this->returnValue(array($randomWord)));
        $loader->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('txt'))
        ;

        $wordlist = new WordList(['/path/to/fake/dictionary.txt']);
        $wordlist->addLoader($loader);

        $this->assertSame($randomWord, $wordlist->getRandomWord());
    }
}
