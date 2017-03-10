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

    /**
     * Function used to set message status as "success"
     * @return ResponseModel
     */
    public function success(): self
    {
        $this->setStatus(true);

        return $this;
    }

    /**
     * Function used to set message status as "failure"
     * @return ResponseModel
     */
    public function failure(): self
    {
        $this->setStatus(false);

        return $this;
    }

    /**
     * Function used to set contents of response
     * @param mixed $content
     * @return ResponseModel
     */
    public function content(mixed $content): self
    {
        $this->setContent($content);

        return $this;
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
     * Internal function used to set response content
     * @param $content
     */
    protected function setContent($content)
    {
        $this->setAttribute(self::RESPONSE_CONTENT_FIELD, $content);
    }
}