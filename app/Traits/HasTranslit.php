<?php
namespace App\Traits;

use App\Utils\Translit;

trait HasTranslit
{
    /**
     * Метод для автоматического заполнения полей перед сохранением
     */
    public function autoFillFields(): void
    {
        $sourceText = (method_exists($this, 'getTranslitSource')) ? $this->getTranslitSource() : $this->name;

        if ($this->isFillable('clearName') && empty($model->clearName))
        {
            $sourceText = Translit::clear($sourceText);
            $this->clearName = $sourceText;
        }

        if (!empty($sourceText) && empty($model->translit))
        {
            $this->translit = Translit::make($sourceText, true, 'Russian-Latin/BGN; Latin-ASCII;');
        }
        // Событие ТРИГГЕР: Перед созданием записи (creating)
        /*Model::creating(function ($model) {
            var_dump("Hello!");
            // Если у модели есть специальный метод для генерации источника
            if (method_exists($model, 'getTranslitSource'))
            {
                $sourceText = $model->getTranslitSource();
            }
            else
            {
                // 2. Если метода нет, берем дефолтное поле 'name'
                $sourceText = $model->name;
            }

            // Метод isFillable() проверяет, разрешено ли это поле к массовому заполнению в модели.
            // Если в модели Author поля clearName нет в массиве $fillable, этот блок просто пропустится
            if ($model->isFillable('clearName') && empty($model->clearName))
            {
                $sourceText = Translit::clear($sourceText);
                $model->clearName = $sourceText;
            }

            if (!empty($sourceText) && empty($model->translit))
            {
                $model->translit = Translit::make($sourceText, true, 'Russian-Latin/BGN; Latin-ASCII;');
            }
        });*/
    }
}

?>