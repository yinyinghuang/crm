<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CountryCodes Model
 *
 * @method \App\Model\Entity\CountryCode get($primaryKey, $options = [])
 * @method \App\Model\Entity\CountryCode newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CountryCode[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CountryCode|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CountryCode patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CountryCode[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CountryCode findOrCreate($search, callable $callback = null, $options = [])
 */
class CountryCodesTable extends Table
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

        $this->setTable('country_codes');
        $this->setDisplayField('country');
        $this->setPrimaryKey('id');

        $this->hasMany('CustomereMobiles', [
            'foreignKey' => 'country_code_id'
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'country_code_id'
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
            ->requirePresence('country', 'create')
            ->notEmpty('country');

        $validator
            ->integer('country_code')
            ->requirePresence('country_code', 'create')
            ->notEmpty('country_code');

        return $validator;
    }
}
