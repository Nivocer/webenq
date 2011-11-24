<?php
class Webenq_Test_Plugin_RequestTest extends Webenq_Test_Case_Plugin
{
    public function testRequestIsUnquotedWhenMagicQuotesAreEnabled()
    {
        $originalMagicQuotesSetting = get_magic_quotes_runtime();
        set_magic_quotes_runtime(true);

        // define key and value
        $key = "test";
        $value = "te\st";

        // simulate post-request with magic quotes enabled
        $request = new Zend_Controller_Request_Http();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request->setPost(addslashes($key), addslashes($value));

        // use plugin
        $plugin = new Webenq_Plugin_Request();
        $plugin->dispatchLoopStartup($request);

        $this->assertTrue($request->getPost($key) === $value);

        // define key and value
        $key = "'test/\\";
        $value = "'test/\\";

        // simulate post-request with magic quotes enabled
        $request = new Zend_Controller_Request_Http();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request->setPost(addslashes($key), addslashes($value));

        // use plugin
        $plugin = new Webenq_Plugin_Request();
        $plugin->dispatchLoopStartup($request);

        $this->assertTrue($request->getPost($key) === $value);

        set_magic_quotes_runtime($originalMagicQuotesSetting);
    }
}