<?php
/**
 * Пакет для работы с большими числами, представленными в виде строк.
 * Пакет написан в тестовых целях
 *
 * User: long
 * Date: 28/10/2018
 * Time: 06:01
 */
namespace bignum;


/**
 * Class BigInt
 * Для работы с большими целыми положителшьными числами.
 * Поддерживаетс исключительно десятичное представление.
 * Скорость работы можно увеличить, если складывать не посимвольно, а большими чанками.
 * Однако, это потребует аккуратной работы с памятью, чтобы не допустить ее переполнение - оставил этот вариант за рамками
 *
 * @package bignum
 */
class BigInt
{
    /**
     * Производит сложение двух целых положительных чисел, представленных в виде строк, результат возвращяется в виде строки
     * В случае если хотя бы одно из чисел отрицательное, либо имеет в строковом представлении символ, отличный от числа,
     * будет брошено исключение, с соответствующим кодом (@see BigIntException)
     *
     * @param string $foo Первое число
     * @param string $bar Второе число
     *
     * @return string
     * @throws BigIntException
     */
    public static function sum(string $foo, string $bar) : string {
        $sum = '';
        $over = 0;

        if( self::isNegative($foo) || self::isNegative($bar) ) {
            throw new BigIntException('Library`s works only positive number', BigIntException::NEGATIVE_NUM);
        }

        $lenFoo = strlen($foo);
        $lenBar = strlen($bar);

        if ( $lenFoo>$lenBar ) {
            $b = $bar;
            [ $a, $stack ] = self::splitByLength($foo, $lenBar);
        }
        elseif ( $lenFoo<$lenBar ) {
            $a = $foo;
            [ $b, $stack ] = self::splitByLength($bar, $lenFoo);
        }
        else {
            $a = $bar;
            $b = $foo;
            $stack = '';
        }

        for ($i=strlen($b)-1;$i>=0; $i--) {
            if( is_numeric($a[$i]) && is_numeric($b[$i]) ){
                [ $s, $over ] = self::_sum(intval($a[$i]), intval($b[$i]), $over);
                $sum = $s . $sum;
            }
            else {
                throw new BigIntException('Invalid number format', BigIntException::NOT_NUMBER);
            }
        }

        if ($over > 0 ) {
            $sum = $over . $sum;
        }

        return $stack.$sum;
    }

    /**
     * Проверка на то, что число является отрицательным
     * @param string $num Число для проверки
     * @return bool
     */
    public static function isNegative(string $num): bool {
        return !self::isPositive($num);
    }

    /**
     * Проверка на то, что число является положительным
     *
     * @param string $num
     * @return bool
     */
    public static function isPositive(string $num) : bool {
        return ($num[0]!=='-') ? true : false;
    }

    /**
     * Сложение двух чисел.
     * @param int $a Первое число
     * @param int $b Второе число
     * @param int $over То, что "держим в уме" от предыдущей операции
     * @return array Возвращает результат в виде массива [младший разряд, старший разря]
     */
    private static function _sum(int $a, int $b, int $over): array {
        $sum = $a+$b+$over;
        if( $sum<=9 ){
            return [$sum, 0];
        }
        else {
            return [$sum-10, 1];
        }
    }

    /**
     * Метод для уравнивания длинны строк.
     * Разбивает строку на младшие и старшие разряды, в дальнейшем сложении участвуют только младшие разряды
     *
     * @param string $s
     * @param int $length
     * @return array
     * @throws BigIntException
     */
    private static function splitByLength(string $s, int $length): array {
        $first = substr($s, 0, strlen($s)-$length);
        for($i=0;$i<strlen($first);$i++) {
            if(!is_numeric($first[$i])) {
                throw new BigIntException('Invalid number format', BigIntException::NOT_NUMBER);
            }
        }
        return [
            substr($s, strlen($s)-$length),
            $first
        ];
    }
}