<?php
class NavHandler extends Base
{
	const Solo = 'solo';

	private $ActiveSection;
	private $Launched;
	private $ContentPanel;
	private $Token;
	private $CreationFunction;
	//For Animations
	private $Animate;
	private $PrevIndex;
	private $Group;
	
	static $Section, $Width, $Height;
	
	function __construct($contentPanel, $creationFunction, $group = null, $tokenName = 'section')
	{
		$this->ContentPanel = $contentPanel;
		$this->Token = $tokenName;
		$this->CreationFunction = $creationFunction;
		$this->Group = $group;
		
		if($this->Token)
		{
			$token = URL::GetToken($this->Token);
			if($token)
			{
				if($group && $group instanceof Group)
					$group->SelectedValue = $token;
			
				$this->LaunchSection($token);
			}
		}
	}
	/**
	 * Tells NavHandler to launch a particular section.
	 *
	 * @param string $section
	 * @param bool|NavHandler::Solo $reCreate
	 * - true: forces recreation of a section
	 * - false: uses a previous instance if any exist
	 * - NavHandler: simply adds the object to the ContentPanel specified WITHOUT keeping track of it. This is useful
	 * for things like windows.
	 * @return Base
	 */
	function LaunchSection($section = null, $reCreate = false)
	{
		if ($section instanceof Group)
		{
			$section = $section->SelectedValue;
		}

		if (!$reCreate && $this->ActiveSection == $section)
		{
			return null;
		}

		$parent = $this->ContentPanel;

		if ($this->Launched[$section] == null || $reCreate)
		{
			self::$Section = $section;
			self::$Width = $parent->Width;
			self::$Height = $parent->Height;

			if ($reCreate !== self::Solo && isset($this->Launched[$section]))
			{
				$this->Launched[$section]->Leave();
			}

			$object = $this->CreationFunction->Exec();
			if ($object)
			{
				if ($reCreate !== self::Solo)
				{
					$this->Launched[$section] = $object;
				}
				$this->ContentPanel->Controls->Add($object);
			}
			else
			{
				$this->ActiveSection = $section;
				return null;
			}
		}
		if ($this->Token)
		{
			URL::SetToken($this->Token, $section);
		}

		if (isset($this->Animate) && isset($this->Group))
		{
			$selectedPosition = $this->Group->SelectedPosition;
		}

		if ($reCreate !== self::Solo)
		{
			if (isset($this->Animate) && $this->PrevIndex !== null)
			{
				$animateLeft = false;
				if (isset($this->Group))
				{
						$animateLeft = $selectedPosition > $this->PrevIndex;
				}
				$activePanel = $this->Launched[$section];
				$properties = $this->Animate == System::Horizontal?array('Left', 'Width'):array('Top', 'Height');
				$magnitude = $activePanel->{$properties[1]};
				$animateProp = $properties[0];
				$activePanel->$animateProp = $animateLeft?$magnitude:(-1 * $magnitude);

				Animate::$animateProp($activePanel, 0, 500);
				if($this->ActiveSection != null)
				{
						$activePanel = $this->Launched[$this->ActiveSection];
						$to = $animateLeft?(-1 * $magnitude):$magnitude;
						Animate::$animateProp($activePanel, $to, 500);
				}
			}
			elseif($this->ActiveSection != null)
			{
				$this->Launched[$this->ActiveSection]->Visible = false;
			}

			if (isset($this->Animate))
			{
				$this->PrevIndex = $selectedPosition;
			}

			$this->ActiveSection = $section;
			$this->Launched[$section]->Visible = true;
		}
	}
	function SetAnimate($animate)
	{
		$this->Animate = $animate;
	}
	function GetContentPanel()
	{
		return $this->ContentPanel;
	}
	function GetActiveSection()
	{
		return $this->ActiveSection;
	}
}
?>
