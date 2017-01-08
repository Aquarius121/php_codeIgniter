<?php

if (!isset($this->vd->period))
	$this->vd->period = 1;

if (!isset($this->vd->silver))
	$this->vd->silver = Model_Item::find_slug('silver-plan');

if (!isset($this->vd->gold))
	$this->vd->gold = Model_Item::find_slug('gold-plan');

if (!isset($this->vd->platinum))
	$this->vd->platinum = Model_Item::find_slug('platinum-plan');

$platinum_plan = Model_Plan::from_item($this->vd->platinum);
$gold_plan = Model_Plan::from_item($this->vd->gold);
$silver_plan = Model_Plan::from_item($this->vd->silver);

$platinum_plan_credits = Model_Plan_Credit::find_all_plan($platinum_plan);
$gold_plan_credits = Model_Plan_Credit::find_all_plan($gold_plan);
$silver_plan_credits = Model_Plan_Credit::find_all_plan($silver_plan);

foreach ($platinum_plan_credits as $credit)
{
	if ($credit->type == Credit::TYPE_PREMIUM_PR)
		$this->vd->platinum->premium_pr_credits = $credit->available;
	if ($credit->type == Credit::TYPE_NEWSROOM)
		$this->vd->platinum->newsroom_credits = $credit->available;
	if ($credit->type == Credit::TYPE_EMAIL)
		$this->vd->platinum->email_credits = $credit->available;
}

foreach ($gold_plan_credits as $credit)
{
	if ($credit->type == Credit::TYPE_PREMIUM_PR)
		$this->vd->gold->premium_pr_credits = $credit->available;
	if ($credit->type == Credit::TYPE_NEWSROOM)
		$this->vd->gold->newsroom_credits = $credit->available;
	if ($credit->type == Credit::TYPE_EMAIL)
		$this->vd->gold->email_credits = $credit->available;
}

foreach ($silver_plan_credits as $credit)
{
	if ($credit->type == Credit::TYPE_PREMIUM_PR)
		$this->vd->silver->premium_pr_credits = $credit->available;
	if ($credit->type == Credit::TYPE_NEWSROOM)
		$this->vd->silver->newsroom_credits = $credit->available;
	if ($credit->type == Credit::TYPE_EMAIL)
		$this->vd->silver->email_credits = $credit->available;
}