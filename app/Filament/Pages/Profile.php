<?php

// namespace App\Filament\Pages;

// use Filament\Pages\Page;
// use Filament\Facades\Filament;
// use Filament\Forms\Components\FileUpload;
// use App\Models\User;

// class Profile extends Page
// {
//     public array $updateProfileInformationState = [];

//     protected static string $view = 'filament.pages.profile';

//     /**
//      * Get the current user of the application.
//      *
//      * @return mixed
//      */
//     public function getUserProperty()
//     {
//         return Filament::auth()->user();
//     }

//     protected function getForms(): array
//     {
//         return [
//             'updateProfileInformationForm' => $this->makeForm()
//                 ->model(User::class)
//                 ->schema([
//                     FileUpload::make('profile_photo_path')
//                         ->image()
//                         ->avatar()
//                         ->disk($this->user->profilePhotoDisk())
//                         ->directory($this->user->profilePhotoDirectory())
//                         ->rules(['nullable', 'mimes:jpg,jpeg,png', 'max:1024'])
//                 ])
//                 ->statePath('updateProfileInformationState'),
//         ];
//     }

//     public function updateProfilePhoto()
//     {
//         $this->user->updateProfilePhoto($this->updateProfileInformationForm->getState());
//     }
// }
