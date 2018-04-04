<?php

Class Bs_card {

	// je kan direct een wp post als 'post' in deze class hangen,
	// per img, titel, tekst het apart zetten of door elkaar.
	// direct ingestelde img, titel, tekst en link waarden overschrijven altijd de
	// uit de post afgeleide waarde.

	function __construct($ar = array()){
		$this->initialiseer($ar);
	}

	private function initialiseer($ar) {

		// ieder waarde => sleutel paar wordt als zodanig in de class, in $this, gezet

		foreach ($ar as $sleutel => $waarde) {
			$this->$sleutel = $waarde;
		}

		$this->controleer_href();
	}

	private function controleer_href () {

		if (!$this->geen_lege_eigenschap('link') and $this->geen_lege_eigenschap('post')) {
			$this->link	= get_the_permalink($this->post->ID);
		}

		$this->heeft_link = $this->geen_lege_eigenschap('link');
	}

	private function open_link($echo = false) {

		// open en sluit link staan op meerdere plekken in de class zodat er tussendoor
		// afwijkende links geplaatst kunnen worden. <a> tags mogen namelijk niet genest worden
		// en als de hele card klikbaar is sluit dat dus afwijkende extra links in de card uit.

		$r = ($this->heeft_link ? "<a href='{$this->link}'>" : '');
		if ($echo) {
			echo $r;
		} else {
			return $r;
		}
	}

	private function sluit_link($echo = false) {
		$r = ($this->heeft_link ? "</a>" : '');
		if ($echo) {
			echo $r;
		} else {
			return $r;
		}
	}

	private function card_class() {

		// uitgedraaid in class attribuut van div.card

		if ($this->geen_lege_eigenschap('card_class')) {
			echo $this->card_class;
		}
	}

	private function geen_lege_eigenschap ($eigenschap = '') {

		// stel dat $eigenschap = 'titel', dan hebben we het over $this->titel
		// deze functie controleert of uberhaupt $this->eigenschap bestaat,
		// en zo ja, of die dan niet leeg is.
		// functie geeft dit als een booleaan terug.

		return (property_exists($this, $eigenschap) and $this->$eigenschap !== '');
	}

	private function img() {

		// dient volledige <img src='' alt='' width='' height='' /> draad te zijn.
		// als niet gegeven, wordt opgehaald uit post, indien die afb heeft.

		if ($this->geen_lege_eigenschap('img')) {
			echo $this->open_link() . $this->img . $this->sluit_link();
		} else if ($this->geen_lege_eigenschap('post')) {
			if (has_post_thumbnail($this->post)) {
				$this->img = get_the_post_thumbnail($this->post, 'card');
				echo $this->img;
			}
		}
	}

	private function titel() {

		if ($this->geen_lege_eigenschap('titel')) {
			echo "<h3 class='card-title'>{$this->titel}</h3>";
		} else if ($this->geen_lege_eigenschap('post')) {
			$this->titel = $this->post->post_title;
			echo "<h3 class='card-title'>{$this->titel}</h3>";
		}

	}

	private function sub_titel() {
		if ($this->geen_lege_eigenschap('sub_titel')) {
			echo $this->sub_titel;
		}
	}

	private function tekst() {
		if ($this->geen_lege_eigenschap('tekst')) {
			echo apply_filters('the_content', $this->tekst);
		} else if ($this->geen_lege_eigenschap('post')) {
			$this->tekst = $this->post->post_content;
			echo apply_filters('the_content', $this->tekst);
		}
	}

	private function knop_class() {
		if ($this->geen_lege_eigenschap('knop_class')) {
			echo $this->knop_class;
		} else {
			echo "btn btn-primary";
		}
	}

	private function knop(){
		if ($this->geen_lege_eigenschap('knop_tekst')) {?>

			<footer class="row">
				<div class="col pt-2 text-center">
				    <?=$this->open_link()?>
				    	<span class="<?=$this->knop_class()?>">
				    		<?=$this->knop_tekst?>
				    	</span>
				    <?=$this->sluit_link()?>
			    </div>
			</footer>
		<?php }

	}

	public function maak_html(){

		// ob_start = output buffer start.
		// maw je produceert normale HTML, maar met de intentie om die later op te vangen. Dit gaat dus nog niet
		// naar de browser. Met ob_get_clean wordt het straks opgevangen en als een draad opgeslagen.

		ob_start();

		?>

		<div class="card <?=$this->card_class()?>">

			<?=$this->img()?>

			<div class="card-body">

				<?php

				echo $this->open_link();

					$this->titel();

					$this->sub_titel();

					$this->tekst();

				echo $this->sluit_link();

				$this->knop();

				?>

			</div>

		</div>

		<?php

		$this->html = ob_get_clean();

		return $this->html;

	}

	public function print(){

		if (!$this->geen_lege_eigenschap('html')) {
			$this->maak_html();
		}

		echo $this->html;

	}

}