<?php
class ConsejoMiembro {
	protected $db;
	public $databaseprefix;
	public $data = array();
	public $post_id;
	public $titulo;
	public $tagline;
	public $titulo_truncado;
	public $slug;
	public $tipo;
	public $descripcion;
	public $extracto;
	public $extra;
	public $categoria;
	public $fecha;
	public $enlaces;
	public $tresdetres = array("","","");
	function __construct($db)
	{
		$this->db = $db;
		$this->databaseprefix = "s_consejo";
	}

	public function init(){
		$this->data['folderpath'] = URLCARGAS.'consejo/obj'.$this->data['post_id'].'/';
		$imagenes = array("cover","thumb","archivo","icono","patrimonial","intereses","fiscal");
		foreach ($imagenes as $imagen) {
			if(isset($this->data[$imagen]) && validaGeneral($this->data[$imagen], 4)){
				$this->data['has_'.$imagen] = true;
				$this->data[$imagen.'_image'] = $this->data['folderpath'].$this->data[$imagen];
				$this->data[$imagen.'_image_medium'] = $this->data['folderpath']."medium_".$this->data[$imagen];
				$this->data[$imagen.'_image_big'] = $this->data['folderpath']."big_".$this->data[$imagen];
			}else{
				$this->data['has_'.$imagen] = false;
				$this->data[$imagen.'_image'] = URLIMAGES.'default/blog_'.$imagen.'.png';
				$this->data[$imagen.'_image_medium'] = URLIMAGES.'default/blog_'.$imagen.'.png';
				$this->data[$imagen.'_image_big'] = URLIMAGES.'default/blog_'.$imagen.'.png';
			}
		}
		$this->titulo = $this->data["titulo"];
		$this->titulo_truncado = truncar_cadena($this->data["titulo"], 76, " ");
		$this->tagline = $this->data["tagline"];
		$this->slug = $this->data["slug"];
		$this->tipo = $this->data["tipo"];
		$this->descripcion = $this->data["descripcion"];
		$this->extracto = truncar_cadena(strip_tags($this->descripcion), 220, ".", '.');
		$this->fecha = formatDate($this->data["modificado"],"formal");
		$this->enlaces = json_decode($this->data['enlaces'], true);
		if($this->data["has_patrimonial"]){
			$this->tresdetres[0] = $this->data["patrimonial_image"];
		}
		if($this->data["has_intereses"]){
			$this->tresdetres[1] = $this->data["intereses_image"];
		}
		if($this->data["has_fiscal"]){
			$this->tresdetres[2] = $this->data["fiscal_image"];
		}
		// $this->url = URL.$this->categoria["categoria_slug"].'/'.$this->slug;
		$this->url = URL.'nosotros/'.$this->slug;
		if(isset($this->data['extra'])){
			$this->extra = json_decode($this->data['extra'], true);
		}
	}
	public function initFromData($data){
		$this->data = $data;
		$this->post_id = $data['post_id'];
		$this->init();
	}
	public function printMiembro(){
		$icono = '';
		$tagline = '';
		if($this->data["has_icono"]){
			$icono = '<img class="nosotros-equipo-icono" src="'.$this->data["icono_image"].'" alt="'.$this->titulo.'">';
		}
		if($this->tagline){
			$tagline = '<div class="nosotros-equipo-tagline">'.$this->tagline.'</div>';
		}
		echo '<div class="nosotros-equipo-box open-miembro" data-open="modal-'.$this->slug.'">
			<div class="nosotros-equipo-img" style="background-image:url('.$this->data["cover_image_big"].');">
				<div class="nosotros-equipo-img-hover">
					<div class="mas-info">
						<i class="fas fa-search-plus"></i>
						<span>Más Información</span>
					</div>
				</div>
				'.$icono.'
			</div>
			<h3>'.$this->titulo.'</h3>
			'.$tagline.'
		</div>';
	}
	public function printModalMiembro(){
		$trestxt = get3de3();
		$tresdetres = '';
		for ($i=0; $i < count($this->tresdetres); $i++) {
			if(!empty($this->tresdetres[$i])){
				$trestxt[$i]["url"] = $this->tresdetres[$i];
				$tresdetres .= '<a class="tdt-'.$i.'" href="'.$trestxt[$i]["url"].'" target="_blank" title="'.$trestxt[$i]["nombre"].'">'.$trestxt[$i]["icono"].'</a>';
			}
		}
		$tresdetreswrap = '';
		if(!empty($tresdetres)){
			$tresdetreswrap = '<div class="modal-tresdetres">
				<img class="tresdetres-logo" src="'.URLIMAGES.'miembro-tresdetres.png">
				'.$tresdetres.'
			</div>';
		}
		$enlaces = '';
		$tweetme = '';
		for ($i=0; $i < count($this->enlaces); $i++) {
			if(!empty($this->enlaces[$i]["url"])){
				$prefix = 'fab';
				if($this->enlaces[$i]["icon"] == "globe"){
					$prefix = 'fas';
				}
				$enlaces .= '<a class="modal-circle-red mcr-'.$this->enlaces[$i]["icon"].'" href="'.$this->enlaces[$i]["url"].'" target="_blank"><i class="'.$prefix.' fa-'.$this->enlaces[$i]["icon"].'"></i></a>';
				if($this->enlaces[$i]["icon"] == "twitter"){
					$handler = $this->getTwitterHandler($this->enlaces[$i]["url"]);
					$tweetme = '<div class="modal-tweetme"><a class="modal-tweet" href="https://twitter.com/intent/tweet?screen_name='.$handler.'&ref_src=twsrc%5Etfw"><i class="fas fa-feather-alt"></i> Escríbeme un tweet</a></div>';
				}
			}
		}
		if(!empty($enlaces)){
			$enlaces = '<div class="modal-enlaces">
				'.$enlaces.$tweetme.'
			</div>';
		}
		$archivo = '';
		if($this->data["has_archivo"]){
			$archivo = '<a href="'.$this->data["archivo_image"].'" class="modal-cv" target="_blank"><img src="'.URLIMAGES.'pdf-icon.png" alt="Descargar CV"><div class="btn-secondary">Descargar CV</div></a>';
		}
		$icono = '';
		if($this->data["has_icono"]){
			$icono = '<img class="modal-cover-icono" src="'.$this->data["icono_image"].'" alt="'.$this->titulo.'">';
		}
		echo '<div class="modal-miembro modal-'.$this->slug.'">
			<div class="wrap">
				<div class="modal-cerrar"><span></span>Cerrar</div>
				<div class="modal-wrap">
					<div class="modal-img">
						<div class="astable">
							<div class="ascell">
								<div class="modal-cover">
									<div class="modal-cover-img" style="background-image:url('.$this->data["cover_image_big"].')">'.$icono.'</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-txt">
						<div class="modal-txt-wrap">
							<h2>'.$this->titulo.'</h2>
							<span class="modal-tagline">'.$this->tagline.'</span>
							<div class="modal-contenido">
								'.$this->descripcion.'
							</div>
							'.$enlaces.'
							<div class="modal-enlaces">
								'.$tresdetreswrap.$archivo.'
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>';
	}
	public function isOpen(){
		if($this->data['status']=="publicado" && $this->data['visibilidad']!="oculto"){
			return true;
		}
		return false;
	}
	public function addView(){
		$this->db->query("UPDATE ".DB_PREFIX.$this->databaseprefix." SET visitas=visitas+1 WHERE post_id = ".$this->post_id);
	}
	public function getTwitterHandler($twitter){
		$parsed = parse_url($twitter);
		$handler = substr($parsed["path"], 1);
		return $handler;
		// https://twitter.com/intent/tweet?screen_name=$handler&ref_src=twsrc%5Etfw
	}
}
?>
