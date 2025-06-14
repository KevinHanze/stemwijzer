<?php

declare(strict_types=1);

namespace Framework\Http;

use Psr\Http\Message\StreamInterface;

final class Stream implements StreamInterface
{
    private string $content; // Alle data in 1 string
    private int $pointer = 0;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function read($length): string
    {
        $result = substr($this->content, $this->pointer, $length);
        $this->pointer += strlen($result);
        return $result;
    }

    public function getContents(): string
    {
        return substr($this->content, $this->pointer);
    }
    public function write(string $string): int
    {
        $this->content .= $string;
        return strlen($string);
    }

    public function getSize(): ?int
    {
        return strlen($this->content);
    }

    public function tell(): int
    {
        return $this->pointer;
    }

    public function eof(): bool
    {
        return $this->pointer >= strlen($this->content);
    }

    public function rewind(): void
    {
        $this->pointer = 0;
    }

    public function isWritable(): bool { return true; }
    public function isReadable(): bool { return true; }
    public function isSeekable(): bool { return true; }

    //
    public function seek($offset, $whence = SEEK_SET): void
    {
        if ($whence === SEEK_SET) {
            $this->pointer = $offset;
        } elseif ($whence === SEEK_CUR) {
            $this->pointer += $offset;
        } elseif ($whence === SEEK_END) {
            $this->pointer = strlen($this->content) + $offset;
        }
    }

    public function detach() {}
    public function close(): void {}
    public function getMetadata($key = null): mixed { return null; }
}
