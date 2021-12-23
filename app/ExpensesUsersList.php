<?php

namespace App;
use App\Expenses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensesUsersList extends Model
{
	protected $table = 'expenses_users_list';
    public function expensesuser()
    {
        return $this->hasMany(Expenses::class);
    }

	
    use HasFactory;
}
