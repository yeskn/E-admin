<?php


namespace Eadmin\form\traits;


trait Validator
{
	/**
	 * 验证最多字符
	 * @param int    $num   字符
	 * @param string $label 名称
	 * @param string $text  文案，必须带上[字段]、[数量]
	 * @return $this
	 */
	public function maxRule($num = 100, $label = '标题', $text = '[字段]不能超过[数量]字')
	{
		$this->formItem->rule([
			"max:$num" => str_replace([
				'[字段]',
				'[数量]',
			], [
				$label,
				$num,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证最少字符
	 * @param int    $num   字符
	 * @param string $label 名称
	 * @param string $text  文案，必须带上[字段]、[数量]
	 * @return $this
	 */
	public function minRule($num = 5, $label = '标题', $text = '[字段]不能超过[数量]字')
	{
		$this->formItem->rule([
			"min:$num" => str_replace([
				'[字段]',
				'[数量]',
			], [
				$label,
				$num,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证邮件有效性
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function emailRule($label = '输入的', $text = '[字段]格式不正确')
	{
		$this->formItem->rule(['email' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证手机有效性
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function mobileRule($label = '输入的', $text = '[字段]格式不正确')
	{
		$this->formItem->rule(['mobile' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证唯一性
	 * @param string $table 表名
	 * @param string $filed 字段
	 * @param string $label 字段名
	 * @param string $text  文案，必须带上[字段]
	 * @return $this
	 */
	public function uniqueRule($table, $filed, $label = '标题', $text = '[字段]已重复')
	{
		$this->formItem->rule(["unique:{$table},{$filed}" => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证身份证有效性
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function idCardRule($label = '输入的', $text = '[字段]不是有效的身份证')
	{
		$this->formItem->rule(['id_card' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证字段是否在某个区间
	 * @param int    $start 开始值
	 * @param int    $end   结束值
	 * @param string $label 字段名
	 * @param string $text  文案，必须带上[开始]、[结束]
	 * @return $this
	 */
	public function betweenRule($start, $end, $label = '输入的值', $text = '[字段]在[开始] - [结束]之间')
	{
		$this->formItem->rule([
			"between:{$start},{$end}" => str_replace([
				'[开始]',
				'[结束]',
				'[字段]',
			], [
				$start,
				$end,
				$label
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证字段是否不在某个区间
	 * @param int    $start 开始值
	 * @param int    $end   结束值
	 * @param string $label 字段名
	 * @param string $text  文案，必须带上[开始]、[结束]
	 * @return $this
	 */
	public function notBetweenRule($start, $end, $label = '输入的值', $text = '[字段]不在[开始] - [结束]之间')
	{
		$this->formItem->rule([
			"notBetween:{$start},{$end}" => str_replace([
				'[开始]',
				'[结束]',
				'[字段]',
			], [
				$start,
				$end,
				$label
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证输入值的是否不一致
	 * @param string $field        被验证字段
	 * @param string $label        被对比的文案
	 * @param string $confirmLabel 对比的文案
	 * @param string $text         文案，必须带上[被对比]、[对比]
	 * @return $this
	 */
	public function confirmRule($field = 'confirm_password', $label = '密码', $confirmLabel = '重复密码', $text = '[被对比]和[对比]输入不一致')
	{
		$this->formItem->rule([
			"confirm:{$field}" => str_replace([
				'[被对比]',
				'[对比]',
			], [
				$label,
				$confirmLabel,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证输入值的是否一致
	 * @param string $field          被验证字段
	 * @param string $label          被对比的文案
	 * @param string $differentLabel 对比的文案
	 * @param string $text           文案，必须带上[被对比]、[对比]
	 * @return $this
	 */
	public function differentRule($field, $label, $differentLabel, $text = '[被对比]和[对比]输入一致')
	{
		$this->formItem->rule([
			"different:{$field}" => str_replace([
				'[被对比]',
				'[对比]',
			], [
				$label,
				$differentLabel,
			], $text),
		]);
		return $this;
	}

	/**
	 * 验证是否是有效的URL
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function urlRule($label = '输入的', $text = '[字段]不是有效的url')
	{
		$this->formItem->rule(['url' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证是否是纯字母
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function alphaRule($label = '输入的', $text = '[字段]不是纯字母')
	{
		$this->formItem->rule(['alpha' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证是否是字母和数字
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function alphaNumRule($label = '输入的', $text = '[字段]不是纯字母和数字')
	{
		$this->formItem->rule(['alphaNum' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证是否是字母和数字，下划线_及破折号-
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function alphaDashRule($label = '输入的', $text = '[字段]不是字母和数字，下划线_及破折号-')
	{
		$this->formItem->rule(['alphaDash' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证是否是汉字
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function chsRule($label = '输入的', $text = '[字段]不是汉字')
	{
		$this->formItem->rule(['chs' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证是否是汉字、字母
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function chsAlphaRule($label = '输入的', $text = '[字段]不是汉字、字母')
	{
		$this->formItem->rule(['chsAlpha' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证是否是汉字、字母和数字
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function chsAlphaNumRule($label = '输入的', $text = '[字段]不是只能是汉字、字母和数字')
	{
		$this->formItem->rule(['chsAlphaNum' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证是否是汉字、字母、数字和下划线_及破折号-
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function chsDashRule($label = '输入的', $text = '[字段]不是汉字、字母、数字和下划线_及破折号-')
	{
		$this->formItem->rule(['chsDash' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证是否是小写字符
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function lowerRule($label = '输入的', $text = '[字段]不是是小写字符')
	{
		$this->formItem->rule(['lower' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证是否是大写字符
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]
	 * @return $this
	 */
	public function upperRule($label = '输入的', $text = '[字段]不是是大写字符')
	{
		$this->formItem->rule(['upper' => str_replace('[字段]', $label, $text)]);
		return $this;
	}

	/**
	 * 验证输入的值是否在范围内
	 * @param string $str 范围
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]、[范围]
	 * @return $this
	 */
	public function inRule($str = '1,2,3', $label = '输入的', $text = '[字段]在[范围]内')
	{
		$this->formItem->rule(["in:{$str}" => str_replace([
			'[字段]',
			'[范围]',
		], [
			$label,
			$str,
		], $text)]);
		return $this;
	}

	/**
	 * 验证输入的值是否不在范围内
	 * @param string $str 范围
	 * @param string $label 字段名
	 * @param string $text 文案，必须带上[字段]、[范围]
	 * @return $this
	 */
	public function notInRule($str = '1,2,3', $label = '输入的', $text = '[字段]在[范围]内')
	{
		$this->formItem->rule(["notIn:{$str}" => str_replace([
			'[字段]',
			'[范围]',
		], [
			$label,
			$str,
		], $text)]);
		return $this;
	}
}