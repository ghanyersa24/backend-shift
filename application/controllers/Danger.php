<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Danger extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$data = array(
			'name' => 'Ghany Abdillah Ersa',
			'contact' => '+6282164028264',
			'email' => 'ghanyersa24@gmail.com',
			'about me' => 'I am good in communication and leadership with analytical thinking. I am used to be a Head of Puskominfo at BEM University of Brawijaya in 2019. I also create and develop digital product named EM Apps (https://em.ub.ac.id/apps) which is used as a tool to fulfil all students need at the university. I am very strong person and able to work under pressure',
			'experience' => array(
				array(
					'tahun' => 2018,
					'organization' => 'Database Administration Laboratory',
					'position' => 'Assistent'
				), array(
					'tahun' => 2018,
					'organization' => 'BEM Universitas Brawijaya ',
					'position' => 'Web Developer '
				), array(
					'tahun' => 2018,
					'organization' => 'Olimpiade Brawijaya ',
					'position' => ' Product Owner OB Apps '
				), array(
					'tahun' => 2019,
					'organization' => ' BEM Universitas Brawijaya ',
					'position' => 'Product Owner EM Apps '
				), array(
					'tahun' => 2020,
					'organization' => 'PT Bejana Investidata Globalindo (BIGIO.ID)',
					'position' => 'Software Quality Assurance '
				)
			)
		);
		success("Hello, this is me.", $data);
	}
	public function not()
	{
		error("Api request is not found.");
	}
}
