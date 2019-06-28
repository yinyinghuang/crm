<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CustomerMobiles Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Customers
 * @property \Cake\ORM\Association\BelongsTo $CountryCodes
 *
 * @method \App\Model\Entity\CustomerMobile get($primaryKey, $options = [])
 * @method \App\Model\Entity\CustomerMobile newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CustomerMobile[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CustomerMobile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CustomerMobile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CustomerMobile[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CustomerMobile findOrCreate($search, callable $callback = null, $options = [])
 */
class CustomerMobilesTable extends Table
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

        $this->setTable('customer_mobiles');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('CountryCodes', [
            'foreignKey' => 'country_code_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('CampaignRecords', [
            'foreignKey' => 'type_FK_id'
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
            ->integer('mobile')
            ->requirePresence('mobile', 'create')
            ->notEmpty('mobile');

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
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));
        $rules->add($rules->existsIn(['country_code_id'], 'CountryCodes'));

        return $rules;
    }
}
