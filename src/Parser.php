<?php

declare(strict_types=1);

namespace HugeJsonCollectionStreamingParser;

use RuntimeException;

class Parser
{
    const STATE_DEFAULT        = 0;
    const STATE_ARRAY_STARTED  = 1;
    const STATE_ARRAY_ENDED    = 2;
    const STATE_OBJECT_STARTED = 3;
    const STATE_OBJECT_ENDED   = 4;
    const STATE_COMMA_DETECTED = 5;

    /**
     * @var bool|resource
     */
    private $stream;

    /**
     * @var int
     */
    private $currentPosition = 0;

    /**
     * @var bool
     */
    private $documentStarted = false;

    /**
     * @var bool
     */
    private $documentEnded = false;

    /**
     * @var string
     */
    private $buffer = '';

    /**
     * @var int
     */
    private $bufferSize;

    /**
     * @var string
     */
    private $lineEnding;

    /**
     * @var int
     */
    private $count = 0;

    /**
     * For debugging
     * @var int
     */
    private $debugCounter = 0;

    /**
     * For debugging
     * @var int
     */
    private $debugCounter2 = 0;

    public function __construct(string $filePath, int $bufferSize = 8192, string $lineEnding = '')
    {
        if (!is_file($filePath)) {
            throw new RuntimeException('Specified file is not exist.');
        }

        $this->stream     = fopen($filePath, 'r');
        $this->bufferSize = $bufferSize;
        $this->lineEnding = $lineEnding;

        $this->verifyStructure();
    }

    public function next(): bool
    {
        if ($this->documentEnded) {
            fclose($this->stream);
            return false;
        }

        if ($this->currentPosition !== 0) {
            fseek($this->stream, $this->currentPosition);
        }

        $singleBackSlash      = '\\';
        $backSlashArray       = [];
        $prevChar             = '';
        $inUserDefinitionArea = false;
        $state                = self::STATE_DEFAULT;
        $level                = 0;

        while (!feof($this->stream)) {

            $pos  = ftell($this->stream);
            $line = stream_get_line($this->stream, $this->bufferSize, $this->lineEnding);

            if ($line === false) {
                return false;
            }

            $byteLen = strlen($line);

            for ($i = 0; $i < $byteLen; ++$i) {

                $char = $line[$i];

                if ($char === $singleBackSlash) {
                    $backSlashArray[] = $char;
                } else {
                    $backSlashArray = [];
                }

                switch ($char) {
                    case '[':
                        if (!$this->documentStarted) {
                            $this->documentStarted = true;
                        } else {
                            if (!$inUserDefinitionArea) {
                                $state = self::STATE_ARRAY_STARTED;
                            }

                            $this->buffer .= $char;
                        }
                        break;
                    case ']':
                        if ($state !== self::STATE_OBJECT_ENDED) {
                            $this->buffer .= $char;
                        } else {
                            $this->documentEnded = true;
                        }

                        if (!$inUserDefinitionArea) {
                            $state = self::STATE_ARRAY_ENDED;
                        }
                        break;
                    case '{':
                        if (!$inUserDefinitionArea) {
                            $level++;
                            $state = self::STATE_OBJECT_STARTED;
                        }

                        $this->buffer .= $char;
                        break;
                    case '}':
                        if (!$inUserDefinitionArea) {
                            $level--;
                            if ($level === 0) {
                                $state = self::STATE_OBJECT_ENDED;
                            }
                        }

                        $this->buffer .= $char;
                        break;
                    case ',':
                        if ($state === self::STATE_OBJECT_ENDED) {
                            $state                 = self::STATE_COMMA_DETECTED;
                            $this->currentPosition = $pos + $i + 1;
                        } else {
                            $this->buffer .= $char;
                        }
                        break;
                    case '"':
                        if (!$inUserDefinitionArea) {
                            $inUserDefinitionArea = true;
                        } else {
                            $inUserDefinitionArea = false;

                            if ($prevChar === $singleBackSlash) {
                                if (count($backSlashArray) % 2 === 1) {
                                    $inUserDefinitionArea = true;
                                } else {
                                    $inUserDefinitionArea = false;
                                }
                            }
                        }

                        $this->buffer .= $char;
                        break;
                    default:
                        $this->buffer .= $char;
                        break;
                }

                if ($state === self::STATE_COMMA_DETECTED) {
                    break 2;
                }

                $prevChar = $char;
            }
        }

        $this->count++;

        return true;
    }

    public function current(): array
    {
        $result = json_decode($this->buffer, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(sprintf('JSON parse failed. Position: %s, Reason: %s', $this->getCurrentPosition(), json_last_error_msg()));
        }

        $this->buffer = '';

        return $result;
    }

    public function setCurrentPosition(int $position)
    {
        $this->currentPosition = $position;

        if ($this->currentPosition !== 0) {
            $this->documentStarted = true;
        }

        return $this;
    }

    public function getCurrentPosition(): int
    {
        return $this->currentPosition;
    }

    private function verifyStructure()
    {
        $chars = '';

        while (true) {
            $char  = stream_get_line($this->stream, 2);
            $char  = trim($char);
            $chars .= $char;

            if (strlen($chars) >= 2) {
                break;
            }
        }

        if (substr($chars, 0, 2) !== '[{') {
            throw new RuntimeException('Invalid JSON structure. Document must start with \'[{\'');
        }

        rewind($this->stream);
    }
}
