<?php

declare(strict_types = 1);

namespace App\Model;


class Message implements \JsonSerializable
{
    /**
     * @var bool|null
     */
    protected $success;
	/**
     * @var int|null
     */
    protected $id;
    /**
     * @var string|null
     */
    protected $message;
    /**
     * @param bool|null $success
     */
    public function setSuccess(?bool $success) : void
    {
        $this->success = $success;
    }
    /**
     * @return bool|null
     */
    public function getSuccess() : ?bool
    {
        return $this->success;
    }
	/**
     * @param int|null $id
     */
    public function setId(?int $id) : void
    {
        $this->id = $id;
    }
    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }
    /**
     * @param string|null $message
     */
    public function setMessage(?string $message) : void
    {
        $this->message = $message;
    }
    /**
     * @return string|null
     */
    public function getMessage() : ?string
    {
        return $this->message;
    }
    public function jsonSerialize()
    {
        return (object) array_filter(array('success' => $this->success, 'id'=>$this->id,'message' => $this->message), static function ($value) : bool {
            return $value !== null;
        });
    }
}
