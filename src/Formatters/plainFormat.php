<?php

namespace Gendiff\Formatters\PlainFormat;

use function Gendiff\SubFunctions\SubFunctions\convertBoolToString;

function getOutputInPlainFormat($diff)
{
    return getOutput($diff);
}

function getOutput($diff, $level = null)
{
    $resultToPrint = array_reduce($diff, function ($acc, $item) use ($level) {
        $level === null ? $level = "{$item['key']}" : $level = "{$level}.{$item['key']}";
        switch ($item['type']) {
            case 'changed':
                $oldValue = transformValueToOutputFormat($item['oldValue']);
                $newValue = transformValueToOutputFormat($item['newValue']);
                $acc[] = "Property '{$level}' was updated. From {$oldValue} to {$newValue}";
                break;
            case 'deleted':
                $value = transformValueToOutputFormat($item['value']);
                $acc[] = "Property '{$level}' was removed";
                break;
            case 'added':
                $value = transformValueToOutputFormat($item['value']);
                $acc[] = "Property '{$level}' was added with value: {$value}";
                break;
            case 'parent':
                $children = getOutput($item['children'], $level);
                $acc[] = $children;
                break;
        }
        return $acc;
    }, []);
    return prepareToOutput($resultToPrint);
}

function prepareToOutput($resultToPrint)
{
    sort($resultToPrint);
    $result = implode("\n", $resultToPrint);
    return $result;
}

function transformValueToOutputFormat($value)
{
    if (is_bool($value)) {
        return convertBoolToString($value);
    }
    if (is_array($value)) {
        return '[complex value]';
    }
    return "'{$value}'";
}
