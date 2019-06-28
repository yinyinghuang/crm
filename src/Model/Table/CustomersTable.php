<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Customers Model
 *
 * @property \Cake\ORM\Association\BelongsTo $CountryCodes
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $Developers
 * @property \Cake\ORM\Association\HasMany $CustomerCommissions
 * @property \Cake\ORM\Association\HasMany $BusinessStatuses
 * @property \Cake\ORM\Association\HasMany $CampaignRecords
 *
 * @method \App\Model\Entity\Customer get($primaryKey, $options = [])
 * @method \App\Model\Entity\Customer newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Customer[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Customer|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Customer patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Customer[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Customer findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CustomersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('customers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Developers', [
            'foreignKey' => 'developer_id',
            'joinType' => 'LEFT'
        ]);
        $this->hasMany('CustomerMobiles', [
            'foreignKey' => 'customer_id'
        ])->setDependent(true);
        $this->hasMany('CustomerEmails', [
            'foreignKey' => 'customer_id'
        ])->setDependent(true);
        $this->hasMany('CustomerCommissions', [
            'foreignKey' => 'customer_id'
        ])->setDependent(true);
        $this->hasMany('BusinessStatuses', [
            'foreignKey' => 'customer_id'
        ])->setDependent(true);
        $this->hasMany('CampaignRecords', [
            'foreignKey' => 'type_FK_id'
        ])->setDependent(true);
        $this->hasMany('CustomerImages', [
            'foreignKey' => 'customer_id'
        ])->setDependent(true);
        $this->hasMany('Businesses', [
            'foreignKey' => 'customer_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');
        $validator
            ->allowEmpty('address');
        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
