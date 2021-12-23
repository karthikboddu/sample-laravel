<?php

namespace App;
use App\User;
use App\ExpensesUsersList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
	protected $table = 'expenses_users';
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
    public function expensesListItem()
    {
    	return $this->belongsTo(ExpensesUsersList::class);
    }
    use HasFactory;
}
