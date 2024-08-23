<?php

class a
{
    public function f($no)
    {
        $data = \App\Models\LekHealth\PaMembers::query()
            ->where(function ($builder) use ($no) {
                $builder
                    ->orWhere(function ($builderz) use ($no) {
                        $builderz->whereLike('name',$no);
                    })
                    ->orWhere(function ($builderz) use ($no) {
                        $builderz->whereLike('account_id',$no);
                    })
                    ->orWhere(function ($builderz) use ($no) {
                        $builderz->whereLike('phone',$no);
                    });
            })
            ->get()->toArray();
        dd($data);
    }
}
$no = 'asd';
(new a())->f($no);


