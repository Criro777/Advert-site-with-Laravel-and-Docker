<?php

namespace App\Services\Profile;

use App\Entity\User;
use App\Http\Requests\Cabinet\ProfileEditRequest;

class ProfileService
{
    /**
     * @param $id
     * @param \App\Http\Requests\Cabinet\ProfileEditRequest $request
     * @throws \Throwable
     */
    public function edit($id, ProfileEditRequest $request): void
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        $oldPhone = $user->phone;
        $user->update($request->only('name', 'surname', 'phone'));
        if ($user->phone !== $oldPhone) {
            $user->unverifyPhone();
        }
    }
}
