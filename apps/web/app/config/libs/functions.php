<?php

function html_decode($text)
{
    return html_entity_decode(h($text));
}