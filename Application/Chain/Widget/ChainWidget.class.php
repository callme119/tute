<?php
namespace Chain\Widget;
use Chain\Model\ChainModel;
use Think\Controller;
class ChainWidget extends Controller
{
	public function getDetailsByIdAction($id)
	{
		$ChainM = new ChainModel();
		$chain = $ChainM->getExamine($id);

		$this->assign('chain',$chain);
		$tpl = T('Chain@Widget/index');
		return $this->fetch($tpl);
	}
}