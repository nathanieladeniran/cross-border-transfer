<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ModelTraits
{
    /**
     * Boot function from Laravel.
     */
    protected static function bootModelTraits()
    {
        static::creating(function ($model) {
            // if (empty($model->{$model->getKeyName()})) {
            //     $model->{$model->getKeyName()} = (string) Str::uuid();
            // }
            if (empty($model->uuid)) { // Make sure you have a 'uuid' attribute in your model
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Initialize the trait for the model.
     */
    public function initializeModelTraits()
    {
        $this->guarded = ['id'];
        $this->setIncrementing(true);
        $this->setKeyType('int');
    }

    /**
     * Set the incrementing property.
     */
    public function setIncrementing($value)
    {
        $this->incrementing = $value;
    }

    /**
     * Set the keyType property.
     */
    public function setKeyType($value)
    {
        $this->keyType = $value;
    }
}
