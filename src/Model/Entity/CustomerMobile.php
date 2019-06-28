<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CustomerMobile Entity
 *
 * @property int $id
 * @property int $customer_id
 * @property int $country_code_id
 * @property string $mobile
 *
 * @property \App\Model\Entity\Customer $customer
 * @property \App\Model\Entity\CountryCode $country_code
 */
class CustomerMobile extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
