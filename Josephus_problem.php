<?php
/* this file is for Josephus problem
 * running time O(m*log n)
 */


/** this is for short number
 * @param int $n
 * @param int $m
 *
 * @return float|int
 */
function solution1(int $n,int $m):int{
    $index = 1;
    $mod = 1;
    while($index<$n){
        $c = floor(abs($index-$mod)/($m-1))+1;
        if($c + $index < $n){
            $index = $index + $c;
            $mod = ( ($mod+$m*$c) % $index ) ?: $index;
        }else{
            return ( ($mod+$m*($n-$index))%$n ) ?: $n;
        }
    }
    return $mod;
}

/** this is for long number ,example 10^20、10^10000
 * @param GMP|string $n
 * @param GMP|string $m
 *
 * @return mixed
 */
function solution2($n,$m){
    $index = 1;
    $mod = 1;
    while(gmp_cmp( $n, $index)){
        $c = gmp_div_q(gmp_sub($index,$mod), ($m-1), GMP_ROUND_MINUSINF);
        $c = gmp_add($c,1);
        if(gmp_cmp( $n,gmp_add($c, $index)) >=0){
            $index = gmp_add($index , $c);
            $mod = gmp_mod(gmp_add($mod, gmp_mul($c,$m)),$index);
            if(gmp_strval($mod) == '0')$mod = $index;
        }else{
            $ret = gmp_mod(gmp_add($mod , gmp_mul($m, gmp_sub($n,$index))) , $n);
            if(gmp_strval($ret) == '0')$ret = $n;
            return gmp_strval($ret);
        }
    }
    return gmp_strval($mod);
}
