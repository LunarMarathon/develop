<?php

namespace Hyde\RealtimeCompiler;

use Hyde\RealtimeCompiler\Actions\CompilesSourceFile;

class Compiler
{
    public string $path;
    public string $output;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->output = $this->makeOutput();
    }

    private function makeOutput()
    {
        $stream = $this->compile();

        // Add any transformations
        $stream = $this->transform($stream);

        return $stream;
    }

    private function compile(): string
    {
        // TODO: Implement compile() method which boots Hyde and compiles the page
        return (new CompilesSourceFile($this->path))->execute();
    }

    private function transform(string $stream): string
    {
        $time = PROXY_START - microtime(true);

        return sprintf("%s<!-- Hyde Realtime Compiler proxied, compiled, and served this request in %sms -->",
            $stream,
            HydeRC::getExecutionTime());
    }

    public function getOutput(): string
    {
        return $this->output;
    }


}