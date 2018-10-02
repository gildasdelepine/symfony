<?php
/**
 * Created by PhpStorm.
 * User: gidel
 * Date: 11/05/18
 * Time: 14:00
 */

namespace SML\PlatformBundle\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class Antiflood
 * @package SML\PlatformBundle\Validator
 * @Annotation
 */
class Antiflood extends Constraint
{
    public $message = "Vous avez déjà posté un message il y a moins de 15 secondes, merci de patienter avant d'en écrire un nouveau.";

    public function validatedBy()
    {
        return 'sml_platform_antiflood';
    }
}