<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Ticket extends Model
{
use HasFactory;
public const TOPICS = ['Git', 'Laravel', 'Blade', 'Database', 'Setup'];
protected $fillable = [
'name',
'topic',
'description',
];
}
