<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AuthNodes Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ParentAuthNodes
 * @property \Cake\ORM\Association\HasMany $ChildAuthNodes
 *
 * @method \App\Model\Entity\AuthNode get($primaryKey, $options = [])
 * @method \App\Model\Entity\AuthNode newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AuthNode[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AuthNode|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AuthNode patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AuthNode[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AuthNode findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TreeBehavior
 */
class AuthNodesTable extends Table
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

        $this->setTable('auth_nodes');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Tree');

        $this->belongsTo('ParentAuthNodes', [
            'className' => 'AuthNodes',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('ChildAuthNodes', [
            'className' => 'AuthNodes',
            'foreignKey' => 'parent_id'
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
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        $validator
            ->requirePresence('routing_address', 'create')
            ->notEmpty('routing_address')
            ->add('routing_address', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->allowEmpty('routing_param');

        $validator
            ->integer('routing_method')
            ->requirePresence('routing_method', 'create')
            ->notEmpty('routing_method');

        $validator
            ->allowEmpty('mark');

        $validator
            ->boolean('is_nav')
            ->requirePresence('is_nav', 'create')
            ->notEmpty('is_nav');

        $validator
            ->allowEmpty('nav_icon');

        $validator
            ->integer('state')
            ->requirePresence('state', 'create')
            ->notEmpty('state');

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
        $rules->add($rules->isUnique(['routing_address']));
        #$rules->add($rules->existsIn(['parent_id'], 'ParentAuthNodes'));

        return $rules;
    }
}
