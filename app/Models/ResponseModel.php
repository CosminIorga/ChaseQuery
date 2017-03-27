<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 02/03/17
 * Time: 11:27
 */

namespace App\Models;


class ResponseModel extends NonPersistentModel
{


    const RESPONSE_SUCCESS_CASE = true;
    const RESPONSE_FAILURE_CASE = false;

    const RESPONSE_STATUS_FIELD = "status";
    const RESPONSE_CONTENT_FIELD = "content";
    const RESPONSE_MESSAGE_FIELD = "message";

    /**
     * ResponseModel constructor.
     */
    public function __construct()
    {
        parent::__construct([]);
    }

    /**
     * Function used to set response status as "success"
     * @return ResponseModel
     */
    public function success(): self
    {
        $this->setStatus(true);

        return $this;
    }

    /**
     * Function used to set response status as "failure"
     * @return ResponseModel
     */
    public function failure(): self
    {
        $this->setStatus(false);

        return $this;
    }

    /**
     * Function used to set response message
     * @param string $message
     * @return ResponseModel
     */
    public function message(string $message): self
    {
        $this->setMessage($message);

        return $this;
    }

    /**
     * Function used to set contents of response
     * @param mixed $content
     * @return ResponseModel
     */
    public function content($content): self
    {
        $this->setContent($content);

        return $this;
    }

    /**
     * Getter for status
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->getAttribute(self::RESPONSE_STATUS_FIELD);
    }

    /**
     * Getter for message
     * @return string
     */
    public function getMessage(): string
    {
        return $this->getAttribute(self::RESPONSE_MESSAGE_FIELD);
    }

    /**
     * Getter for content
     * @return mixed
     */
    public function getContent()
    {
        return $this->getAttribute(self::RESPONSE_CONTENT_FIELD);
    }

    /**
     * Internal function used to set status either "success" or "failure"
     * @param bool $status
     */
    protected function setStatus(bool $status)
    {
        $this->setAttribute(self::RESPONSE_STATUS_FIELD, $status);
    }

    /**
     * Internal function used to set message to given string
     * @param string $message
     */
    protected function setMessage(string $message)
    {
        $this->setAttribute(self::RESPONSE_MESSAGE_FIELD, $message);
    }

    /**
     * Internal function used to set response content
     * @param mixed $content
     */
    protected function setContent($content)
    {
        $this->setAttribute(self::RESPONSE_CONTENT_FIELD, $content);
    }
}