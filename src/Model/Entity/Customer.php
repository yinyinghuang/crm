<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Customer Entity
 *
 * @property int $id
 * @property string $name
 * @property int $country_code_id
 * @property string $mobile
 * @property string $email
 * @property string $address
 * @property int $user_id
 * @property int $developer_id
 * @property int $source
 * @property bool $state
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\CountryCode $country_code
 * @property \App\Model\Entity\Developer $developer
 * @property \App\Model\Entity\CustomerCommission[] $customer_commissions
 * @property \App\Model\Entity\BusinessStatus[] $business_statuses
 */
class Customer extends Entity
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
