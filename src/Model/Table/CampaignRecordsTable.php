<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CampaignRecords Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Campaigns
 * @property \Cake\ORM\Association\BelongsTo $Customers
 *
 * @method \App\Model\Entity\CampaignRecord get($primaryKey, $options = [])
 * @method \App\Model\Entity\CampaignRecord newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CampaignRecord[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CampaignRecord|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CampaignRecord patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CampaignRecord[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CampaignRecord findOrCreate($search, callable $callback = null, $options = [])
 */
class CampaignRecordsTable extends Table
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

        $this->setTable('campaign_records');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Campaigns', [
            'foreignKey' => 'campaign_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Customers', [
            'foreignKey' => 'type_FK_id',
            'joinType' => 'INNER'
        ]);
        
        $this->belongsTo('CustomerMobiles', [
            'foreignKey' => 'type_FK_id',
            'joinType' => 'INNER'
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
            ->requirePresence('result', 'create')
            ->notEmpty('result');

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
        $rules->add($rules->existsIn(['campaign_id'], 'Campaigns'));
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));

        return $rules;
    }
}
