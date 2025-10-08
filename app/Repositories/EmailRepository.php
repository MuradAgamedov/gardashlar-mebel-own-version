<?php
namespace App\Repositories;

use App\Models\Email;
use App\Repositories\Global\BaseRepository;

class EmailRepository extends BaseRepository
{

    public function __construct(public Email $email)
    {
    }

    // məsələn bütün məlumatları gətirmək üçün
    public function getEmails()
    {
        return $this->GetData($this->email, [])->get();
    }
}
