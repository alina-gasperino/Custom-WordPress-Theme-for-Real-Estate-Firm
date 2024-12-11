<?php
/**
 * Class TalkCondo MongoDB Wrapper
 */
class tcdb {
	/**
	 * List of TalkCondo collections
	 * @var array
	 */
	var $collections = array( 'projects', 'floorplans', 'developers', 'neighborhoods', 'cities' );

	/**
	 * TalkCondo Projects collection
	 *
	 * @var MongoDB\Collection
	 */
	public $projects;

	/**
	 * TalkCondo Floor Plans collection
	 *
	 * @var MongoDB\Collection
	 */
	public $floorplans;

	/**
	 * TalkCondo Developers collection
	 *
	 * @var MongoDB\Collection
	 */
	public $developers;

	/**
	 * TalkCondo Neighborhoods collection
	 *
	 * @var MongoDB\Collection
	 */
	public $neighborhoods;

	/**
	 * TalkCondo Cities collection
	 *
	 * @var MongoDB\Collection
	 */
	public $cities;

	/**
	 * WordPress collection prefix
	 *
	 * You can set this to have multiple WordPress installations
	 * in a single database. The second reason is for possible
	 * security precautions.
	 *
	 * @var string
	 */
	public $prefix = '';

	/**
	 * Database Username
	 *
	 * @var string
	 */
	protected $dbuser;

	/**
	 * Database Password
	 *
	 * @var string
	 */
	protected $dbpassword;

	/**
	 * Database Name
	 *
	 * @var string
	 */
	protected $dbname;

	/**
	 * Database Host
	 *
	 * @var string
	 */
	protected $dbhost;

	/**
	 * Database Instance
	 *
	 * @var string
	 */
	protected $dbh;

	/**
	 * Connects to the MongoDB server and selects a database
	 *
	 * PHP5 style constructor for compatibility with PHP5. Does
	 * the actual setting up of the class properties and connection
	 * to the database.
	 *
	 * @link https://core.trac.wordpress.org/ticket/3354
	 * @since 2.0.8
	 *
	 * @global string $wp_version
	 *
	 * @param string $dbuser MySQL database user
	 * @param string $dbpassword MySQL database password
	 * @param string $dbname MySQL database name
	 * @param string $dbhost MySQL database host
	 */
	public function __construct( $dbuser, $dbpassword, $dbname, $dbhost = '127.0.0.1' ) {
		$this->dbuser     = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname     = $dbname;
		$this->dbhost     = $dbhost;

		$success = $this->db_connect();

		if( is_wp_error( $success ) ){
			wp_die( $success );
		}
	}

	/**
	 *
	 * @return true|WP_Error
	 */
	public function db_connect() {
		if ( ! extension_loaded( 'mongodb' ) ) {
			return new WP_Error( 'MONGO_EXTENSION_MISSING', "The MongoDB extension is missing or not loaded." );
		}

		if( empty( $this->dbname ) ){
			return new WP_Error( 'MONGO_MISSING_CONFIG', 'No database was specified in the wp-config.php file.' );
		}

		$options = array();

		if( ! empty( $dbuser ) ) {
			$options['authSource'] = $this->dbname;
			$options['username'] = $this->dbuser;

			if ( ! empty( $this->dbpassword ) ) {
				$options['password'] = $this->dbpassword;
			}
		}

		try {
			$cli = new MongoDB\Client( sprintf( 'mongodb://%s/', $this->dbhost ), $options );
		} catch( \MongoDB\Driver\Exception\RuntimeException $e ){
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}

		try {
			$this->dbh = $cli->selectDatabase( $this->dbname );
		} catch( \MongoDB\Exception\InvalidArgumentException $e ){
			return new WP_Error( 'MONGODB_INVALID_DATABASE', sprintf( '<strong>%s</strong> is not a valid database.', $this->dbname ) );
		}

		// Set default collection prefix
		return $this->set_prefix();
	}

	/**
	 * Sets the collection prefix for TalkCondo collections
	 *
	 * @param string $prefix
	 *
	 * @return true|WP_Error
	 */
	protected function set_prefix( $prefix = '' ) {
		global $wpdb;

		$prefix = $prefix ?: $wpdb->get_blog_prefix();

		if ( preg_match( '|[^a-z0-9_]|i', $prefix ) ) {
			return new WP_Error( 'invalid_db_prefix', 'Invalid database prefix' );
		}

		$this->prefix = $prefix;

		foreach ( $this->collections as $collection ) {
			$this->{$collection} = $prefix . $collection;
		}

		return true;
	}

	/**
	 * Returns a collection
	 *
	 * @param string $collection The name of the collection
	 * @return MongoDB\Collection|false The collection handler or false on failure
	 */
	public function get_collection( $collection ){
		if( in_array( $collection, $this->collections ) ){
			return $this->dbh->selectCollection( $this->{$collection} );
		}

		return false;
	}

	public function find_one( $collection, $filter, $options = [] ){
		$hdl = $this->get_collection( $collection );

		if ( ! $hdl ) {
			return new WP_Error( 'TCDB_Invalid_Collection', 'Invalid collection specified.' );
		}

		return $hdl->findOne( $filter, $options );
	}

	public function find_many( $collection, $filter, $options = [] ){
		$hdl = $this->get_collection( $collection );

		if ( ! $hdl ) {
			return new WP_Error( 'TCDB_Invalid_Collection', 'Invalid collection specified.' );
		}

		return $hdl->find( $filter, $options );
	}

	public function replace_one( $collection, $filter, $replacement, $options = [] ) {
		$hdl = $this->get_collection( $collection );

		if ( ! $hdl ) {
			return new WP_Error( 'TCDB_Invalid_Collection', 'Invalid collection specified.' );
		}

		return $hdl->replaceOne( $filter, $replacement, $options );
	}

	public function update( $collection, $filter, $update, $options = [] ){
		$options = wp_parse_args( $options, [ 'upsert' => true ] );

		$hdl = $this->get_collection( $collection );

		if ( ! $hdl ) {
			return new WP_Error( 'TCDB_Invalid_Collection', 'Invalid collection specified.' );
		}

		if( count( $update ) > 1 ) {
			return $hdl->updateMany( $filter, $update, $options );
		}

		return $hdl->updateOne( $filter, $update, $options );
	}

	public function insert( $collection, $document, $options = [] ){
		$hdl = $this->get_collection( $collection );

		if ( ! $hdl ) {
			return new WP_Error( 'TCDB_Invalid_Collection', 'Invalid collection specified.' );
		}

		if( count( $document ) > 1 ) {
			return $hdl->insertMany( $document, $options );
		}

		return $hdl->insertOne( $document, $options );
	}

	public function empty( $collection, $options = [] ){
		$hdl = $this->get_collection( $collection );

		if ( ! $hdl ) {
			return new WP_Error( 'TCDB_Invalid_Collection', 'Invalid collection specified.' );
		}

		return $hdl->deleteMany( [], $options );
	}

	/**
	 * @param $collection
	 * @param $pipeline
	 * @param array $options
	 *
	 * @return Traversable|WP_Error
	 */
	public function aggregate( $collection, $pipeline, $options = [] ){
		$hdl = $this->get_collection( $collection );

		if ( ! $hdl ) {
			return new WP_Error( 'TCDB_Invalid_Collection', 'Invalid collection specified.' );
		}

		return $hdl->aggregate( $pipeline, $options );
	}
}

/**
 * Load the talkcondo database class file and instantiate the `$tcdb` global.
 *
 * @global tcdb $tcdb The WordPress database class.
 */
function require_tc_db() {
	global $tcdb;

	require_once __DIR__ . '/vendor/autoload.php';

	if ( isset( $tcdb ) ) {
		return;
	}

	$tcdb = new tcdb( MONGO_USER, MONGO_PASSWORD, MONGO_NAME, MONGO_HOST );
}

global $tcdb;
require_tc_db();
