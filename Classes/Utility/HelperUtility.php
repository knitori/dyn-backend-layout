<?php
/**
 * Created by PhpStorm.
 * User: Lars_Soendergaard
 * Date: 29.06.2016
 * Time: 12:21
 */

namespace LFM\Lfmtheme\Utility;

class HelperUtility
{
    public static function Lcm($a, $b)
    {
        if ($a == 0 || $b == 0) {
            return 0;
        }
        return ($a * $b) / self::Gcd($a, $b);
    }

    public static function Gcd($a, $b)
    {
        do {
            $r = $a % $b;
            $a = $b;
            $b = $r;
        } while ($b != 0);
        return $a;
    }

    /**
     * Iterate over string yielding one char after another.
     * Bonus: add unicode codepoint as key.
     * (makes it useless with iterator_to_array function though)
     *
     * @param string $str
     * @param string $encoding
     * @return \Generator
     */
    public static function IterStr($str, $encoding=null)
    {
        if(is_null($encoding)) {
            $encoding = mb_internal_encoding();
        }
        for($i=0; $i<mb_strlen($str, $encoding); $i++) {
            $char = mb_substr($str, $i, 1, $encoding);
            list(, $unicode) = unpack('N', mb_convert_encoding($char, 'UCS-4BE', $encoding));
            yield $unicode => $char;
        }
    }

    /**
     * python zip()
     * https://docs.python.org/3.4/library/functions.html#zip
     *
     * @param array ...$arrays
     * @return \Generator|void
     * @throws \Exception
     */
    public static function Zip(...$arrays)
    {
        /** @var \Iterator[] $arrIter */
        $arrIter = [];
        // Convert all non-iterators to iterator, to get a unified interface.
        foreach(self::Enumerate($arrays, 1) as list($argIndex, $array)) {
            if ($array instanceof \Iterator) {
                $arrIter[] = $array;
            }
            else if(is_array($array)) {
                $arrIter[] = new \ArrayIterator($array);
            }
            else if(is_string($array)) {
                $arrIter[] = self::IterStr($array);
            }
            else {
                throw new \InvalidArgumentException(
                    "Don't know how to deal with argument #".$argIndex." of type '".gettype($array)."'.");
            }
        }
        while(true) {
            $tpl = [];
            foreach($arrIter as $iter) {
                if(!$iter->valid()) {
                    return;
                }
            }
            foreach($arrIter as $i => $iter) {
                $tpl[] = $iter->current();
                $iter->next();
            }
            yield $tpl;
        }
    }

    /**
     * python enumerate()
     * https://docs.python.org/3.4/library/functions.html#enumerate
     *
     * @param \Iterator|array $iterable
     * @param int $start
     * @return \Generator
     */
    public static function Enumerate($iterable, $start=0)
    {
        foreach($iterable as $key => $value) {
            yield $key => [$start++, $value];
        }
    }

    /**
     * python map()
     * https://docs.python.org/3.4/library/functions.html#map
     *
     * @param callable $callback
     * @param array ...$iterables
     * @return \Generator
     */
    public static function Map($callback, ...$iterables)
    {
        foreach(self::Zip(...$iterables) as $args) {
            yield $callback(...$args);
        }
    }

    /**
     * python filter()
     * https://docs.python.org/3.4/library/functions.html#filter
     *
     * @param callable $callback
     * @param \Iterator|array $iterable
     * @return \Generator
     */
    public static function Filter($callback, $iterable)
    {
        foreach($iterable as $element) {
            if($callback($element)) {
                yield $element;
            }
        }
    }

    /**
     * Just counts forward.
     *
     * @param int $start
     * @return \Generator
     */
    public static function Count($start=0)
    {
        while (true) {
            yield $start++;
        }
    }
}
