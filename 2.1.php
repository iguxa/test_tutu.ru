<?php
/**
 * Created by PhpStorm.
 * User: it-iguxa
 * Date: 2018-08-22
 * Time: 17:06
 *
 * Насколько я понял необходимо было реализовать чтобы скидка высчитывлась пропорционально из цены каждого товара к
 * его части в итоговой стоимости,так же по ограничениям:минимальная скидка 100р ,а максимальная 500р,когда сумма заказа
 * свыше 10000р,так же скидка 5%. Я реализовал систему купонов,те исходя из купона будет применена та или иная скидка,
 * так же если итоговая цена меньше минимальной скидки,то скидка не применяется.
 */
class User
{
    public function getAge(): int {
        return mt_rand(15, 80);
    }
}
//применить действие исходя из типов копона
const ACTION = ['buy'=>'minus','elderly'=>'percent'];
//купоны
const DISCOUNT_TYPE = ['buy'=>['min'=>100,'max'=>500],
                       'elderly'=>0.95];
//максимальная цена с которой начинает действовать максимальная скидка
const MAX_BUY = 10000;

class Cart
{
    private $_user;
    private $_items = [];
    private $discount_type;
    private $total_price;
    private $price_discount;

    public function __construct(User $user)
    {
        $this->_user = $user;
    }

    public function getUser(): User
    {
        return $this->_user;
    }
    // item_id, price, sku, etc.
    public function addItem(array $item)
    {
        $this->_items[] = $item;
        self::getTotalAmount();//получение полной стоимости без учета скидки при добавлении товара в корзину
    }
    public function getItem(){
        return $this->_items;
    }
    //получение полной стоимости без учета скидки при добавлении товара в корзину
    public function getTotalAmount(): int
    {
        $ret = 0;
        foreach ($this->_items as $item)
        {$ret += $item['price'];}
        $this->total_price = $ret;
        return  $this->total_price;
    }
    //получение полной стоимости с учетом скидки
    public function getDiscountedTotalAmount(): ?int
    {
        $items = $this->_items;
        $items_count = count($items)-1;
        $discount_info = $this->discount_type;
        $action = false;
        $ret = null;

        //получаем тип действия исходя из скидки
        if(is_array($discount_info)){
            $action = key($discount_info);}

        if($action == 'minus'){
            $total_price = $this->total_price;
            $max_buy = MAX_BUY;//максимальная цена с которой идет макимальная скидка
            $min_discount =  $discount_info['minus']['min'];
            $max_discount =  $discount_info['minus']['max'];
            $discount = 0;

            //определение скидки исходя из итоговой стоимости
            //если итоговая стоимость меньше минимальной скидки,скидка не применятся
            if($total_price <= $max_buy and $total_price > $min_discount){
                $discount = $min_discount;}
            elseif($total_price > $max_buy){
                $discount = $max_discount;
            }
            //применеие скидки пропорционально к итоговой стоимости исходя из стоимости каждого товара
            for($i=0;$i<=$items_count;$i++){
                $discount_part =$items[$i]['price']/$total_price;
                $discount_price = $items[$i]['price'] - ($discount*$discount_part);
                $this->_items[$i]['discount'] = round($discount_price,2);
                $ret += $this->_items[$i]['discount'];
            }
            $this->price_discount = $ret;
        }
        if($action == 'percent'){
            $discount =  $discount_info['percent'];
            //применеие скидки пропорционально к итоговой стоимости исходя из стоимости каждого товара
            for($i=0;$i<=$items_count;$i++){
                $discount_price = $items[$i]['price']*$discount;
                $this->_items[$i]['discount'] = round($discount_price,2);
                $ret += $this->_items[$i]['discount'];
            }
            $this->price_discount = $ret;
        }
        return  $this->price_discount;
    }
    public function _getDiscount()
    {
        return  $this->discount_type;
    }
    //получение скидки согласно купону
    public function getDiscountType($discount) 
    {
        $actions = ACTION; //действие исходя из DISCOUNT_TYPE
        $discount_type = DISCOUNT_TYPE;//купоны
        //получение скидки и действия исходя из переданного купона
        foreach ($discount_type as $key => $value){
            if($key == $discount){
                foreach ($actions as $type => $action){
                    if($type == $key){
                    $this->discount_type[$action] = $value;
                    }
                }
            }
        }
        return $this;
    }
}

$item1 = ['item_id'=>1,'price'=>1000,'sku'=>123];
$item2 = ['item_id'=>2,'price'=>2000,'sku'=>456];
$item3 = ['item_id'=>3,'price'=>3000,'sku'=>789];

$user = new User();
$product = new Cart($user);

$product->addItem($item1);
$product->addItem($item2);
$product->addItem($item3);
echo '<pre>';


var_dump($product->getTotalAmount());
var_dump($product->getDiscountType('buy')->getDiscountedTotalAmount());
var_dump($product->getItem());