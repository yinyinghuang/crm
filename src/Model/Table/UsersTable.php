<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Departments
 * @property \Cake\ORM\Association\BelongsTo $Roles
 * @property \Cake\ORM\Association\BelongsTo $CountryCodes
 * @property \Cake\ORM\Association\HasMany $BusinessStatuses
 * @property \Cake\ORM\Association\HasMany $Businesses
 * @property \Cake\ORM\Association\HasMany $Campaigns
 * @property \Cake\ORM\Association\HasMany $CustomerCommissions
 * @property \Cake\ORM\Association\HasMany $Customers
 * @property \Cake\ORM\Association\HasMany $Developers
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'LEFT'
        ]);
        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('CountryCodes', [
            'foreignKey' => 'country_code_id',
            'joinType' => 'LEFT'
        ]);
        $this->hasMany('BusinessStatuses', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Businesses', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Campaigns', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('CustomerCommissions', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Customers', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Developers', [
            'foreignKey' => 'user_id'
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
            ->requirePresence('userid', 'create')
            ->notEmpty('userid');

        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password');

        $validator
            ->allowEmpty('gender');

        $validator
            ->requirePresence('mobile', 'create')
            ->notEmpty('mobile');

        $validator
            ->boolean('state')
            ->allowEmpty('state');

        $validator
            ->date('last_login')
            ->allowEmpty('last_login');

        $validator
            ->dateTime('last_op')
            ->allowEmpty('last_op');

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
        $rules->add($rules->isUnique(['userid']));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['role_id'], 'Roles'));
        $rules->add($rules->existsIn(['country_code_id'], 'CountryCodes'));

        return $rules;
    }
    public function findAuth(\Cake\ORM\Query $query, array $options)
    {
        $query->where(['Users.state' => 1]);
        return $query;
    }

}
