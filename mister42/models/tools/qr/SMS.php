<?php

namespace mister42\models\tools\qr;

class SMS extends Phone
{
    public function generateQr(): bool
    {
        return parent::generate("SMS:{$this->phone}");
    }
}
