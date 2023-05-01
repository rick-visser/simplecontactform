<?php namespace Exposia\SimpleContactForm\Models;

use Model;

/**
 * Model
 */
class Contact extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    /**
     * @var array dates to cast from the database.
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string table in the database used by the model.
     */
    public $table = 'exposia_simplecontactform_message';

    /**
     * @var array rules for validation.
     */
    public $rules = [
    ];

    // Fillable fields
    public $fillable = ['firstname', 'lastname', 'email', 'phone', 'company', 'content'];

}
