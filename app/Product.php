<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{
    protected $guarded = [];
    //Relations Methods 
    public function Category(){
        return $this->belongsTo(Category::class)->withDefault([
            'title' => 'Deleted Category',
            'slug' => 'deleted-category',
            'image' => 'category.png',
            'description' => 'Deleted Category'
        ]);
    }

    //Non-Relation Methods
    public function getInventoryValueAttribute(){
        if($this->fake_inventory == 0){
            return $this->inventory;
        }else{
            if($this->inventory > $this->fake_inventory){
                return $this->fake_inventory;
            }else{
                return $this->inventory;
            }
        }
    }
    public function getIsActiveAttribute(){
        if($this->status == 'Available'){
            return true;
        }else{
            return false;
        }
    }
    public function getLocalTitleAttribute(){
        $SiteLang = \Lang::locale() ?? 'en';
        if($SiteLang == 'en'){
            return $this->title;
        }else{
            return Product_Local::where('product_id' , $this->id)->where('lang_code' , $SiteLang)->first()->title_value;
        }
    }
    public function getLocalSlugAttribute(){
        $SiteLang = \Lang::locale() ?? 'en';
        if($SiteLang == 'en'){
            return $this->slug;
        }else{
            return Product_Local::where('product_id' , $this->id)->where('lang_code' , $SiteLang)->first()->slug_value;
        }
    }
    public function getLocalDescriptionAttribute(){
        $SiteLang = \Lang::locale() ?? 'en';
        if($SiteLang == 'en'){
            return $this->description;
        }else{
            return Product_Local::where('product_id' , $this->id)->where('lang_code' , $SiteLang)->first()->description_value;
        }
    }
    public function getLocalBodyAttribute(){
        $SiteLang = \Lang::locale() ?? 'en';
        if($SiteLang == 'en'){
            return $this->body;
        }else{
            return Product_Local::where('product_id' , $this->id)->where('lang_code' , $SiteLang)->first()->body_value;
        }
    }
    public function getMainImageAttribute(){
        return url('storage/app/images/products').'/'.$this->image;
    }
    public function GalleryImages(){
        return $this->hasMany(Product_Image::class);
    }
    public function LikedByUser(){
        if(auth()->check()){
            $isLiked = Favourite::where('user_id' , auth()->user()->id)->where('product_id' , $this->id)->count();
            if($isLiked != 0){
                return true;
            }else{
                return false;
            }
        }
    }
    public function getStatusClassAttribute(){
        $StatuesArray = [];
        if($this->status == 'Sold Out'){
            $StatuesArray['text'] = 'text-danger';
            $StatuesArray['background'] = 'bg-danger';
        }elseif($this->status == 'Available'){
            $StatuesArray['text'] = 'text-success';
            $StatuesArray['background'] = 'bg-success';
        }elseif($this->status == 'Pre-Order'){
            $StatuesArray['text'] = 'text-warning';
            $StatuesArray['background'] = 'bg-warning';
        }else{
            $StatuesArray['text'] = 'd-none';
            $StatuesArray['background'] = 'd-none';
        }
        return $StatuesArray;
    }
}
