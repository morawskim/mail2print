<?php

namespace mail2print\Models\Reply;


interface Notify
{
    public function send($content);
}