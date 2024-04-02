<?php

namespace Ixolit\Dislo\Redirector;

use Ixolit\Dislo\Exceptions\RedirectorException;
use Ixolit\Dislo\Redirector\Base\Factory;
use Ixolit\Dislo\Redirector\Rules\Conditions\Condition;
use Ixolit\Dislo\Redirector\Rules\Rule;
use Ixolit\Dislo\Redirector\Rules\RuleConditionNode;
use Ixolit\Dislo\Redirector\Rules\RuleNode;

/**
 * Class RulesParser
 * @package Ixolit\Dislo\Redirector
 */
class RulesParser
{

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * RulesInitializer constructor.
     */
    public function __construct()
    {
        $this->factory = new Factory();
    }

    /**
     * @param string $json
     * @return Rule[]
     */
    public function buildRulesFromJson($json) {

        if (empty($json)) {
            return [];
        }

        $data = json_decode($json, true);

        return $this->buildRulesFromData($data);
    }

    /**
     * @param mixed $data
     * @return Rule[]
     */
    public function buildRulesFromData($data) {
        if (!is_array($data) || empty($data['redirectorRules'])) {
            return [];
        }

        $rulesResult = [];
        foreach ($data['redirectorRules'] as $ruleData) {
            try {
                $rule = new Rule();
                $rule->setName($ruleData['name']);
                if ($ruleData['ruleNodes']) {
                    $rule->setRootRuleNode($this->buildNode($ruleData['ruleNodes']));
                }

                $rulesResult[] = $rule;
            } catch (RedirectorException $e) {
                // ignore this rule
            }
        }

        return $rulesResult;
    }

    /**
     * @param array $nodeData
     * @return RuleNode|null
     */
    protected function buildNode($nodeData) {

        if ($nodeData['type'] === 'condition') {
            return $this->buildConditionNode($nodeData);
        }

        return $this->buildActionNode($nodeData);
    }

    /**
     * @param array $nodeData
     * @return RuleConditionNode|null
     */
    private function buildConditionNode($nodeData)
    {
        $node = new RuleConditionNode();
        $node->setMatching($nodeData['matching'] === RuleConditionNode::MATCHING_AND ? RuleConditionNode::MATCHING_AND : RuleConditionNode::MATCHING_OR);
        $conditions = $this->buildConditions($nodeData['conditions']);

        $node->setConditions($conditions);
        if (!empty($nodeData['then'])) {
            $node->setThen($this->buildNode($nodeData['then']));
        }
        if (!empty($nodeData['else'])) {
            $node->setElse($this->buildNode($nodeData['else']));
        }
        if (!empty($nodeData['next'])) {
            $node->setNext($this->buildNode($nodeData['next']));
        }

        return $node;
    }

    /**
     * @param array $nodeData
     * @return Action|null
     */
    private function buildActionNode($nodeData)
    {
        $node = $this->factory->createActionFromArray($nodeData);

        if (!empty($nodeData['next'])) {
            $node->setNext($this->buildNode($nodeData['next']));
        }

        return $node;
    }

    /**
     * @param RuleConditionNode[]|null $conditionsData
     * @return Condition[]
     */
    protected function buildConditions($conditionsData) {
        if (!$conditionsData || !is_array($conditionsData)) {
            return [];
        }

        $ruleConditionNodes = [];
        foreach ($conditionsData as $conditionData) {
            $ruleConditionNode = $this->factory->createConditionFromArray($conditionData);
            $ruleConditionNodes[] = $ruleConditionNode;
        }

        return $ruleConditionNodes;
    }

}
