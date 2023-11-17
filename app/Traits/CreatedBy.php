<?php
namespace App\Traits;

use App\Models\User;

trait CreatedBy {
	public function created_by()
	{
		return $this->belongsTo(User::class, 'created_by');
	}
	public function approved_by()
	{
		return $this->belongsTo(User::class, 'approved_by');
	}
	public function updated_by()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}
	public function deleted_by()
	{
		return $this->belongsTo(User::class, 'deleted_by');
	}
}
