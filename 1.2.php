<?php
/**
 * Created by PhpStorm.
 * User: it-iguxa
 * Date: 2018-07-25
 * Time: 14:38
 *
 * Насколько я понял все зависит от массы которую мы можем забрать с собой,то первый пункт (можно взять не более одного
 * камня каждого типа) можно взять оба, либо один зависит от массы,в приоритете самый дорогой.Второй пункт
 * (можно брать сколько угодно камней каждого типа) соответсвенно отличается от первого тем что нет ограничений по количеству
 * камней.
 */

//свойства камней
const STONES_ONE = ['m'=>3,'p'=>2,'type'=>'first_stone'];
const STONES_TWO = ['m'=>11,'p'=>3,'type'=>'second_stone'];

class Treasure{

    private $first_stone;
    private $second_stone;

    //определение первого и второго камня исходя из цены камней
    public function __construct(){
        $this->first_stone = (STONES_ONE['p']>STONES_TWO['p'])?STONES_ONE:STONES_TWO;
        $this->second_stone = (STONES_ONE['p']<STONES_TWO['p'])?STONES_ONE:STONES_TWO;
    }
    //определяем какой камень или оба камня возьмем исходя из цены и переданного максимально веса,но не более чем по одному камню каждого вида
    public function SingleStones($maxM):array
    {
        $first_stone = $this->first_stone;
        $second_stone = $this->second_stone;
        $total_first_stone = [];
        $total_second_stone = [];
        $result = [];
        //проходят ли камни по переданному нами весу
        if($first_stone['m']<=$maxM){
            $total_first_stone['price'] = $first_stone['p'];
            $total_first_stone['weight'] = $first_stone['m'];
            $total_first_stone['type'] = $first_stone['type'];
        }
        if($second_stone['m']<=$maxM){
            $total_second_stone['price'] = $second_stone['p'];
            $total_second_stone['weight'] = $second_stone['m'];
            $total_second_stone['type'] = $second_stone['type'];
        }

        $result['price'] = 0;
        $result['weight'] = 0;
        //вычисляем стоимость обоих камней при условии,что по весу они проходят
        while($maxM >= $result['weight'] and ($result['weight'] + $first_stone['m']) <= $maxM){
                $result['price'] += $first_stone['p'];
                $result['weight'] += $first_stone['m'];
                $result['type'] = $first_stone['type'];
                if (($result['weight'] + $second_stone['m']) <= $maxM) {
                    $result['price'] += $second_stone['p'];
                    $result['weight'] += $second_stone['m'];
                    $result['type'] = 'mixes';
                }
                break;
        }

        return max($total_first_stone,$total_second_stone,$result);//выбираем выриант который нас наиболее устраивает
    }
    //определяем какой камень или оба камня возьмем исходя из цены и переданного максимально веса
    public function MaxStones($maxM):array
    {
        $first_stone = $this->first_stone;
        $second_stone = $this->second_stone;

        $total_first_stone = [];
        $total_second_stone = [];
        $result = [];

        //формируем итог по первому и второму камням раздельно
        $total_first_stone['price'] = $first_stone['p']* intval($maxM/$first_stone['m']);
        $total_first_stone['weight'] = intval($maxM/$first_stone['m']);
        $total_first_stone['type'] = $first_stone['type'];

        $total_second_stone['price'] = $second_stone['p']* intval($maxM/$second_stone['m']);
        $total_second_stone['weight'] = intval($maxM/$second_stone['m']);
        $total_second_stone['type'] = $second_stone['type'];

        $result['price'] = 0;
        $result['weight'] = 0;

        //формируем итог по первому и второму камням,вместе
        while($maxM>=$result['weight'] and ($result['weight'] + $first_stone['m']) <= $maxM){
            $result['price'] += $first_stone['p'];
            $result['weight'] += $first_stone['m'];
            $result['type'] = $first_stone['type'];
            if (($result['weight'] + $second_stone['m']) <= $maxM) {
                $result['price'] += $second_stone['p'];
                $result['weight'] += $second_stone['m'];
                $result['type'] = 'mixes';
            }
        }
        return max($total_first_stone,$total_second_stone,$result);//выбираем выриант который нас наиболее устраивает
    }
}
$treasure = new Treasure();
echo '<pre>';
var_dump( $treasure->SingleStones(11));
var_dump( $treasure->MaxStones(11));
