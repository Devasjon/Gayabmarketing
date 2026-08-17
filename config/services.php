<?php
return [
  'postmark'=>['token'=>env('POSTMARK_TOKEN')],
  'resend'=>['key'=>env('RESEND_KEY')],
  'ses'=>['key'=>env('AWS_ACCESS_KEY_ID'),'secret'=>env('AWS_SECRET_ACCESS_KEY'),'region'=>env('AWS_DEFAULT_REGION','us-east-1')],
  'billplz'=>[
    'api_key'=>env('BILLPLZ_API_KEY'),
    'collection_id'=>env('BILLPLZ_COLLECTION_ID'),
    'x_signature'=>env('BILLPLZ_X_SIGNATURE'),
    'endpoint'=>env('BILLPLZ_ENDPOINT','https://www.billplz-sandbox.com/api'),
    'checkout_enabled'=>(bool)env('BILLPLZ_CHECKOUT_ENABLED',false),
  ],
];

