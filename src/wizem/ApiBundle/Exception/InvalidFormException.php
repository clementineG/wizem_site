<?php

namespace wizem\ApiBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidFormException extends HttpException 
{
    protected $form;

    public function __construct($message, $form = null)
    {
        parent::__construct(500, $message);
        $this->form = $form;
    }

    /**
     * @return array|null
     */
    public function getForm()
    {
        return $this->form;
    }
}