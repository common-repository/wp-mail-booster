<?php // @codingStandardsIgnoreLine
/**
 * This file Pure-PHP X.509 Parser.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * Pure-PHP X.509 Parser
 *
 * PHP version 5
 *
 * Encode and decode X.509 certificates.
 *
 *
 * Note that loading an X.509 certificate and resaving it may invalidate the signature.  The reason being that the signature is based on a
 * portion of the certificate that contains optional parameters with default values.  ie. if the parameter isn't there the default value is
 * used.  Problem is, if the parameter is there and it just so happens to have the default value there are two ways that that parameter can
 * be encoded.  It can be encoded explicitly or left out all together.  This would effect the signature value and thus may invalidate the
 * the certificate all together unless the certificate is re-signed.
 */

namespace phpseclib\File;

use phpseclib\Crypt\Hash;
use phpseclib\Crypt\Random;
use phpseclib\Crypt\RSA;
use phpseclib\File\ASN1\Element;
use phpseclib\Math\BigInteger;

/**
 * Pure-PHP X.509 Parser
 *
 * @package X509
 * @access  public
 */
class X509 {
	/**
	 * Flag to only accept signatures signed by certificate authorities
	 *
	 * Not really used anymore but retained all the same to suppress E_NOTICEs from old installs
	 *
	 * @access public
	 */
	const VALIDATE_SIGNATURE_BY_CA = 1;

	/**
	 * Return internal array representation
	 */
	const DN_ARRAY = 0;
	/**
	 * Return string
	 */
	const DN_STRING = 1;
	/**
	 * Return ASN.1 name string
	 */
	const DN_ASN1 = 2;
	/**
	 * Return OpenSSL compatible array
	 */
	const DN_OPENSSL = 3;
	/**
	 * Return canonical ASN.1 RDNs string
	 */
	const DN_CANON = 4;
	/**
	 * Return name hash for file indexing
	 */
	const DN_HASH = 5;

	/**
	 * Save as PEM
	 * ie. a base64-encoded PEM with a header and a footer
	 */
	const FORMAT_PEM = 0;
	/**
	 * Save as DER
	 */
	const FORMAT_DER = 1;
	/**
	 * Save as a SPKAC
	 *
	 * Only works on CSRs. Not currently supported.
	 */
	const FORMAT_SPKAC = 2;
	/**
	 * Auto-detect the format
	 *
	 * Used only by the load*() functions
	 */
	const FORMAT_AUTO_DETECT = 3;
	/**#@-*/

	/**
	 * Attribute value disposition.
	 * If disposition is >= 0, this is the index of the target value.
	 */
	const ATTR_ALL     = -1; // All attribute values (array).
	const ATTR_APPEND  = -2; // Add a value.
	const ATTR_REPLACE = -3; // Clear first, then add a value.

	/**
	 * ASN.1 syntax for X.509 certificates
	 *
	 * @var array
	 * @access private
	 */
	var $Certificate; // @codingStandardsIgnoreLine.

	/**
	 * Variable for directory string
	 *
	 * @access private
	 * @var string
	 */
	var $DirectoryString; // @codingStandardsIgnoreLine.
	/**
	 * Variable for string
	 *
	 * @access private
	 * @var string
	 */
	var $PKCS9String; // @codingStandardsIgnoreLine.
	/**
	 * Variable for attribute value
	 *
	 * @access private
	 * @var string
	 */
	var $AttributeValue; // @codingStandardsIgnoreLine.
	/**
	 * Variable for extension
	 *
	 * @access private
	 * @var string
	 */
	var $Extensions; // @codingStandardsIgnoreLine.
	/**
	 * Variable for key usage
	 *
	 * @access private
	 * @var string
	 */
	var $KeyUsage; // @codingStandardsIgnoreLine.
	/**
	 * Variable for ext key usage syntax
	 *
	 * @access private
	 * @var string
	 */
	var $ExtKeyUsageSyntax; // @codingStandardsIgnoreLine.
	/**
	 * Variable for basic constraints
	 *
	 * @access private
	 * @var string
	 */
	var $BasicConstraints; // @codingStandardsIgnoreLine.
	/**
	 * Variable for key identifier
	 *
	 * @access private
	 * @var string
	 */
	var $KeyIdentifier; // @codingStandardsIgnoreLine.
	/**
	 * Variable for crl distribution point
	 *
	 * @access private
	 * @var string
	 */
	var $CRLDistributionPoints; // @codingStandardsIgnoreLine.
	/**
	 * Variable for authority key identifier
	 *
	 * @access private
	 * @var string
	 */
	var $AuthorityKeyIdentifier; // @codingStandardsIgnoreLine.
	/**
	 * Variable for certificate policy
	 *
	 * @access private
	 * @var string
	 */
	var $CertificatePolicies; // @codingStandardsIgnoreLine.
	/**
	 * Variable for authority access info syntax
	 *
	 * @access private
	 * @var string
	 */
	var $AuthorityInfoAccessSyntax; // @codingStandardsIgnoreLine.
	/**
	 * Variable for subject alt name
	 *
	 * @access private
	 * @var string
	 */
	var $SubjectAltName; // @codingStandardsIgnoreLine.
	/**
	 * Variable for subject directory attribute
	 *
	 * @access private
	 * @var string
	 */
	var $SubjectDirectoryAttributes; // @codingStandardsIgnoreLine.
	/**
	 * Variable for private key usage page
	 *
	 * @access private
	 * @var string
	 */
	var $PrivateKeyUsagePeriod; // @codingStandardsIgnoreLine.
	/**
	 * Variable for issuer alt name
	 *
	 * @access private
	 * @var string
	 */
	var $IssuerAltName; // @codingStandardsIgnoreLine.
	/**
	 * Variable for policy mapping
	 *
	 * @access private
	 * @var string
	 */
	var $PolicyMappings; // @codingStandardsIgnoreLine.
	/**
	 * Variable for constraints name
	 *
	 * @access private
	 * @var string
	 */
	var $NameConstraints; // @codingStandardsIgnoreLine.
	/**
	 * Variable for cps uri
	 *
	 * @access private
	 * @var string
	 */
	var $CPSuri; // @codingStandardsIgnoreLine.
	/**
	 * Variable for user notice
	 *
	 * @access private
	 * @var string
	 */
	var $UserNotice; // @codingStandardsIgnoreLine.
	/**
	 * Variable for netscape cert type
	 *
	 * @access private
	 * @var string
	 */
	var $netscape_cert_type; // @codingStandardsIgnoreLine.
	/**
	 * Variable for netscape comment
	 *
	 * @access private
	 * @var string
	 */
	var $netscape_comment; // @codingStandardsIgnoreLine.
	/**
	 * Variable for netscape policy url
	 *
	 * @access private
	 * @var string
	 */
	var $netscape_ca_policy_url; // @codingStandardsIgnoreLine.
	/**
	 * Variable for
	 *
	 * @access private
	 * @var string
	 */
	var $Name; // @codingStandardsIgnoreLine.
	/**
	 * Variable for relative distinguish name
	 *
	 * @access private
	 * @var string
	 */
	var $RelativeDistinguishedName; // @codingStandardsIgnoreLine.
	/**
	 * Variable for crl number
	 *
	 * @access private
	 * @var string
	 */
	var $CRLNumber; // @codingStandardsIgnoreLine.
	/**
	 * Variable for crl reason
	 *
	 * @access private
	 * @var string
	 */
	var $CRLReason; // @codingStandardsIgnoreLine.
	/**
	 * Variable for issuing distribuion point
	 *
	 * @access private
	 * @var string
	 */
	var $IssuingDistributionPoint; // @codingStandardsIgnoreLine.
	/**
	 * Variable for sign public key
	 *
	 * @access private
	 * @var string
	 */
	var $InvalidityDate; // @codingStandardsIgnoreLine.
	/**
	 * Variable for certificate issuer
	 *
	 * @access private
	 * @var string
	 */
	var $CertificateIssuer; // @codingStandardsIgnoreLine.
	/**
	 * Variable for hold instruction code .
	 *
	 * @access private
	 * @var string
	 */
	var $HoldInstructionCode; // @codingStandardsIgnoreLine.
	/**
	 * Variable for sign public key
	 *
	 * @access private
	 * @var string
	 */
	var $SignedPublicKeyAndChallenge; // @codingStandardsIgnoreLine.
	/**
	 * ASN.1 syntax for various DN attributes
	 *
	 * @access private
	 * @var string
	 */
	var $PostalAddress; // @codingStandardsIgnoreLine.
	/**
	 * ASN.1 syntax for Certificate Signing Requests (RFC2986)
	 *
	 * @var array
	 * @access private
	 */
	var $CertificationRequest; // @codingStandardsIgnoreLine.

	/**
	 * ASN.1 syntax for Certificate Revocation Lists (RFC5280)
	 *
	 * @var array
	 * @access private
	 */
	var $CertificateList; // @codingStandardsIgnoreLine.

	/**
	 * Distinguished Name
	 *
	 * @var array
	 * @access private
	 */
	var $dn; // @codingStandardsIgnoreLine.

	/**
	 * Public key
	 *
	 * @var string
	 * @access private
	 */
	var $publicKey; // @codingStandardsIgnoreLine.

	/**
	 * Private key
	 *
	 * @var string
	 * @access private
	 */
	var $privateKey; // @codingStandardsIgnoreLine.

	/**
	 * Object identifiers for X.509 certificates
	 *
	 * @var array
	 * @access private
	 * @link http://en.wikipedia.org/wiki/Object_identifier
	 */
	var $oids; // @codingStandardsIgnoreLine.

	/**
	 * The certificate authorities
	 *
	 * @var array
	 * @access private
	 */
	var $CAs; // @codingStandardsIgnoreLine.

	/**
	 * The currently loaded certificate
	 *
	 * @var array
	 * @access private
	 */
	var $currentCert; // @codingStandardsIgnoreLine.

	/**
	 * The signature subject
	 *
	 * There's no guarantee \phpseclib\File\X509 is going to re-encode an X.509 cert in the same way it was originally
	 * encoded so we take save the portion of the original cert that the signature would have made for.
	 *
	 * @var string
	 * @access private
	 */
	var $signatureSubject; // @codingStandardsIgnoreLine.

	/**
	 * Certificate Start Date
	 *
	 * @var string
	 * @access private
	 */
	var $startDate; // @codingStandardsIgnoreLine.

	/**
	 * Certificate End Date
	 *
	 * @var string
	 * @access private
	 */
	var $endDate; // @codingStandardsIgnoreLine.

	/**
	 * Serial Number
	 *
	 * @var string
	 * @access private
	 */
	var $serialNumber; // @codingStandardsIgnoreLine.

	/**
	 * Key Identifier
	 *
	 * @var string
	 * @access private
	 */
	var $currentKeyIdentifier; // @codingStandardsIgnoreLine.

	/**
	 * CA Flag
	 *
	 * @var bool
	 * @access private
	 */
	var $caFlag = false; // @codingStandardsIgnoreLine.

	/**
	 * SPKAC Challenge
	 *
	 * @var string
	 * @access private
	 */
	var $challenge; // @codingStandardsIgnoreLine.

	/**
	 * Default Constructor.
	 *
	 * @return \phpseclib\File\X509
	 * @access public
	 */
	public function __construct() {
		$this->DirectoryString = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_CHOICE,
			'children' => array(
				'teletexString'   => array( 'type' => ASN1::TYPE_TELETEX_STRING ),
				'printableString' => array( 'type' => ASN1::TYPE_PRINTABLE_STRING ),
				'universalString' => array( 'type' => ASN1::TYPE_UNIVERSAL_STRING ),
				'utf8String'      => array( 'type' => ASN1::TYPE_UTF8_STRING ),
				'bmpString'       => array( 'type' => ASN1::TYPE_BMP_STRING ),
			),
		);

		$this->PKCS9String = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_CHOICE,
			'children' => array(
				'ia5String'       => array( 'type' => ASN1::TYPE_IA5_STRING ),
				'directoryString' => $this->DirectoryString, // @codingStandardsIgnoreLine.
			),
		);

		$this->AttributeValue = array( 'type' => ASN1::TYPE_ANY ); // @codingStandardsIgnoreLine.

		$AttributeType = array( 'type' => ASN1::TYPE_OBJECT_IDENTIFIER ); // @codingStandardsIgnoreLine.

		$AttributeTypeAndValue = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'type'  => $AttributeType, // @codingStandardsIgnoreLine.
				'value' => $this->AttributeValue, // @codingStandardsIgnoreLine.
			),
		);

		/*
		In practice, RDNs containing multiple name-value pairs (called "multivalued RDNs") are rare,
		but they can be useful at times when either there is no unique attribute in the entry or you
		want to ensure that the entry's DN contains some useful identifying information.

		*/
		$this->RelativeDistinguishedName = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SET,
			'min'      => 1,
			'max'      => -1,
			'children' => $AttributeTypeAndValue, // @codingStandardsIgnoreLine.
		);

		$RDNSequence = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			// RDNSequence does not define a min or a max, which means it doesn't have one .
			'min'      => 0,
			'max'      => -1,
			'children' => $this->RelativeDistinguishedName, // @codingStandardsIgnoreLine.
		);

		$this->Name = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_CHOICE,
			'children' => array(
				'rdnSequence' => $RDNSequence, // @codingStandardsIgnoreLine.
			),
		);

		$AlgorithmIdentifier = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'algorithm'  => array( 'type' => ASN1::TYPE_OBJECT_IDENTIFIER ),
				'parameters' => array(
					'type'     => ASN1::TYPE_ANY,
					'optional' => true,
				),
			),
		);

		$Extension = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'extnId'    => array( 'type' => ASN1::TYPE_OBJECT_IDENTIFIER ),
				'critical'  => array(
					'type'     => ASN1::TYPE_BOOLEAN,
					'optional' => true,
					'default'  => false,
				),
				'extnValue' => array( 'type' => ASN1::TYPE_OCTET_STRING ),
			),
		);

		$this->Extensions = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'min'      => 1,
			// technically, it's MAX, but we'll assume anything < 0 is MAX .
			'max'      => -1,
			// if 'children' isn't an array then 'min' and 'max' must be defined .
			'children' => $Extension, // @codingStandardsIgnoreLine.
		);

		$SubjectPublicKeyInfo = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'algorithm'        => $AlgorithmIdentifier, // @codingStandardsIgnoreLine.
				'subjectPublicKey' => array( 'type' => ASN1::TYPE_BIT_STRING ),
			),
		);

		$UniqueIdentifier = array( 'type' => ASN1::TYPE_BIT_STRING ); // @codingStandardsIgnoreLine.

		$Time = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_CHOICE,
			'children' => array(
				'utcTime'     => array( 'type' => ASN1::TYPE_UTC_TIME ),
				'generalTime' => array( 'type' => ASN1::TYPE_GENERALIZED_TIME ),
			),
		);

		$Validity = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'notBefore' => $Time, // @codingStandardsIgnoreLine.
				'notAfter'  => $Time, // @codingStandardsIgnoreLine.
			),
		);

		$CertificateSerialNumber = array( 'type' => ASN1::TYPE_INTEGER ); // @codingStandardsIgnoreLine.

		$version = array(
			'type'    => ASN1::TYPE_INTEGER,
			'mapping' => array( 'v1', 'v2', 'v3' ),
		);

		$TBSCertificate = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'version'              => array(
					'constant' => 0,
					'optional' => true,
					'explicit' => true,
					'default'  => 'v1',
				) + $version,
				'serialNumber'         => $CertificateSerialNumber, // @codingStandardsIgnoreLine.
				'signature'            => $AlgorithmIdentifier, // @codingStandardsIgnoreLine.
				'issuer'               => $this->Name, // @codingStandardsIgnoreLine.
				'validity'             => $Validity, // @codingStandardsIgnoreLine.
				'subject'              => $this->Name, // @codingStandardsIgnoreLine.
				'subjectPublicKeyInfo' => $SubjectPublicKeyInfo, // @codingStandardsIgnoreLine.
				// implicit means that the T in the TLV structure is to be rewritten, regardless of the type .
				'issuerUniqueID'       => array(
					'constant' => 1,
					'optional' => true,
					'implicit' => true,
				) + $UniqueIdentifier, // @codingStandardsIgnoreLine.
				'subjectUniqueID'      => array(
					'constant' => 2,
					'optional' => true,
					'implicit' => true,
				) + $UniqueIdentifier, // @codingStandardsIgnoreLine.
				// it's not IMPLICIT, it's EXPLICIT
				'extensions'           => array(
					'constant' => 3,
					'optional' => true,
					'explicit' => true,
				) + $this->Extensions, // @codingStandardsIgnoreLine.
			),
		);

		$this->Certificate = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'tbsCertificate'     => $TBSCertificate, // @codingStandardsIgnoreLine.
				'signatureAlgorithm' => $AlgorithmIdentifier, // @codingStandardsIgnoreLine.
				'signature'          => array( 'type' => ASN1::TYPE_BIT_STRING ),
			),
		);

		$this->KeyUsage = array( // @codingStandardsIgnoreLine.
			'type'    => ASN1::TYPE_BIT_STRING,
			'mapping' => array(
				'digitalSignature',
				'nonRepudiation',
				'keyEncipherment',
				'dataEncipherment',
				'keyAgreement',
				'keyCertSign',
				'cRLSign',
				'encipherOnly',
				'decipherOnly',
			),
		);

		$this->BasicConstraints = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'cA'                => array(
					'type'     => ASN1::TYPE_BOOLEAN,
					'optional' => true,
					'default'  => false,
				),
				'pathLenConstraint' => array(
					'type'     => ASN1::TYPE_INTEGER,
					'optional' => true,
				),
			),
		);

		$this->KeyIdentifier = array( 'type' => ASN1::TYPE_OCTET_STRING ); // @codingStandardsIgnoreLine.

		$OrganizationalUnitNames = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'min'      => 1,
			'max'      => 4, // ub-organizational-units .
			'children' => array( 'type' => ASN1::TYPE_PRINTABLE_STRING ),
		);

		$PersonalName = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SET,
			'children' => array(
				'surname'              => array(
					'type'     => ASN1::TYPE_PRINTABLE_STRING,
					'constant' => 0,
					'optional' => true,
					'implicit' => true,
				),
				'given-name'           => array(
					'type'     => ASN1::TYPE_PRINTABLE_STRING,
					'constant' => 1,
					'optional' => true,
					'implicit' => true,
				),
				'initials'             => array(
					'type'     => ASN1::TYPE_PRINTABLE_STRING,
					'constant' => 2,
					'optional' => true,
					'implicit' => true,
				),
				'generation-qualifier' => array(
					'type'     => ASN1::TYPE_PRINTABLE_STRING,
					'constant' => 3,
					'optional' => true,
					'implicit' => true,
				),
			),
		);

		$NumericUserIdentifier = array( 'type' => ASN1::TYPE_NUMERIC_STRING ); // @codingStandardsIgnoreLine.

		$OrganizationName = array( 'type' => ASN1::TYPE_PRINTABLE_STRING ); // @codingStandardsIgnoreLine.

		$PrivateDomainName = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_CHOICE,
			'children' => array(
				'numeric'   => array( 'type' => ASN1::TYPE_NUMERIC_STRING ),
				'printable' => array( 'type' => ASN1::TYPE_PRINTABLE_STRING ),
			),
		);

		$TerminalIdentifier = array( 'type' => ASN1::TYPE_PRINTABLE_STRING ); // @codingStandardsIgnoreLine.

		$NetworkAddress = array( 'type' => ASN1::TYPE_NUMERIC_STRING ); // @codingStandardsIgnoreLine.

		$AdministrationDomainName = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_CHOICE,
			// if class isn't present it's assumed to be \phpseclib\File\ASN1::CLASS_UNIVERSAL or
			// (if constant is present) \phpseclib\File\ASN1::CLASS_CONTEXT_SPECIFIC .
			'class'    => ASN1::CLASS_APPLICATION,
			'cast'     => 2,
			'children' => array(
				'numeric'   => array( 'type' => ASN1::TYPE_NUMERIC_STRING ),
				'printable' => array( 'type' => ASN1::TYPE_PRINTABLE_STRING ),
			),
		);

		$CountryName = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_CHOICE,
			// if class isn't present it's assumed to be \phpseclib\File\ASN1::CLASS_UNIVERSAL or
			// (if constant is present) \phpseclib\File\ASN1::CLASS_CONTEXT_SPECIFIC .
			'class'    => ASN1::CLASS_APPLICATION,
			'cast'     => 1,
			'children' => array(
				'x121-dcc-code'        => array( 'type' => ASN1::TYPE_NUMERIC_STRING ),
				'iso-3166-alpha2-code' => array( 'type' => ASN1::TYPE_PRINTABLE_STRING ),
			),
		);

		$AnotherName = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'type-id' => array( 'type' => ASN1::TYPE_OBJECT_IDENTIFIER ),
				'value'   => array(
					'type'     => ASN1::TYPE_ANY,
					'constant' => 0,
					'optional' => true,
					'explicit' => true,
				),
			),
		);

		$ExtensionAttribute = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'extension-attribute-type'  => array(
					'type'     => ASN1::TYPE_PRINTABLE_STRING,
					'constant' => 0,
					'optional' => true,
					'implicit' => true,
				),
				'extension-attribute-value' => array(
					'type'     => ASN1::TYPE_ANY,
					'constant' => 1,
					'optional' => true,
					'explicit' => true,
				),
			),
		);

		$ExtensionAttributes = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SET,
			'min'      => 1,
			'max'      => 256, // ub-extension-attributes .
			'children' => $ExtensionAttribute, // @codingStandardsIgnoreLine.
		);

		$BuiltInDomainDefinedAttribute = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'type'  => array( 'type' => ASN1::TYPE_PRINTABLE_STRING ),
				'value' => array( 'type' => ASN1::TYPE_PRINTABLE_STRING ),
			),
		);

		$BuiltInDomainDefinedAttributes = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'min'      => 1,
			'max'      => 4, // ub-domain-defined-attributes .
			'children' => $BuiltInDomainDefinedAttribute, // @codingStandardsIgnoreLine.
		);

		$BuiltInStandardAttributes =  array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'country-name'               => array( 'optional' => true ) + $CountryName, // @codingStandardsIgnoreLine.
				'administration-domain-name' => array( 'optional' => true ) + $AdministrationDomainName, // @codingStandardsIgnoreLine.
				'network-address'            => array(
					'constant' => 0,
					'optional' => true,
					'implicit' => true,
				) + $NetworkAddress, // @codingStandardsIgnoreLine.
				'terminal-identifier'        => array(
					'constant' => 1,
					'optional' => true,
					'implicit' => true,
				) + $TerminalIdentifier, // @codingStandardsIgnoreLine.
				'private-domain-name'        => array(
					'constant' => 2,
					'optional' => true,
					'explicit' => true,
				) + $PrivateDomainName, // @codingStandardsIgnoreLine.
				'organization-name'          => array(
					'constant' => 3,
					'optional' => true,
					'implicit' => true,
				) + $OrganizationName, // @codingStandardsIgnoreLine.
				'numeric-user-identifier'    => array(
					'constant' => 4,
					'optional' => true,
					'implicit' => true,
				) + $NumericUserIdentifier, // @codingStandardsIgnoreLine.
				'personal-name'              => array(
					'constant' => 5,
					'optional' => true,
					'implicit' => true,
				) + $PersonalName, // @codingStandardsIgnoreLine.
				'organizational-unit-names'  => array(
					'constant' => 6,
					'optional' => true,
					'implicit' => true,
				) + $OrganizationalUnitNames, // @codingStandardsIgnoreLine.
			),
		);

		$ORAddress = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'built-in-standard-attributes' => $BuiltInStandardAttributes, // @codingStandardsIgnoreLine.
				'built-in-domain-defined-attributes' => array( 'optional' => true ) + $BuiltInDomainDefinedAttributes, // @codingStandardsIgnoreLine.
				'extension-attributes'         => array( 'optional' => true ) + $ExtensionAttributes // @codingStandardsIgnoreLine.
			),
		);

		$EDIPartyName = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'nameAssigner' => array(
					'constant' => 0,
					'optional' => true,
					'implicit' => true,
				) + $this->DirectoryString, // @codingStandardsIgnoreLine.
				// partyName is technically required but \phpseclib\File\ASN1 doesn't currently support non-optional constants and
				// setting it to optional gets the job done in any event.
				'partyName'    => array(
					'constant' => 1,
					'optional' => true,
					'implicit' => true,
				) + $this->DirectoryString, // @codingStandardsIgnoreLine.
			),
		);

		$GeneralName = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_CHOICE,
			'children' => array(
				'otherName'                 => array(
					'constant' => 0,
					'optional' => true,
					'implicit' => true,
				) + $AnotherName, // @codingStandardsIgnoreLine.
				'rfc822Name'                => array(
					'type'     => ASN1::TYPE_IA5_STRING,
					'constant' => 1,
					'optional' => true,
					'implicit' => true,
				),
				'dNSName'                   => array(
					'type'     => ASN1::TYPE_IA5_STRING,
					'constant' => 2,
					'optional' => true,
					'implicit' => true,
				),
				'x400Address'               => array(
					'constant' => 3,
					'optional' => true,
					'implicit' => true,
				) + $ORAddress, // @codingStandardsIgnoreLine.
				'directoryName'             => array(
					'constant' => 4,
					'optional' => true,
					'explicit' => true,
				) + $this->Name, // @codingStandardsIgnoreLine.
				'ediPartyName'              => array(
					'constant' => 5,
					'optional' => true,
					'implicit' => true,
				) + $EDIPartyName, // @codingStandardsIgnoreLine.
				'uniformResourceIdentifier' => array(
					'type'     => ASN1::TYPE_IA5_STRING,
					'constant' => 6,
					'optional' => true,
					'implicit' => true,
				),
				'iPAddress'                 => array(
					'type'     => ASN1::TYPE_OCTET_STRING,
					'constant' => 7,
					'optional' => true,
					'implicit' => true,
				),
				'registeredID'              => array(
					'type'     => ASN1::TYPE_OBJECT_IDENTIFIER,
					'constant' => 8,
					'optional' => true,
					'implicit' => true,
				),
			),
		);

		$GeneralNames = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'min'      => 1,
			'max'      => -1,
			'children' => $GeneralName, // @codingStandardsIgnoreLine.
		);

		$this->IssuerAltName = $GeneralNames; // @codingStandardsIgnoreLine.

		$ReasonFlags = array( // @codingStandardsIgnoreLine.
			'type'    => ASN1::TYPE_BIT_STRING,
			'mapping' => array(
				'unused',
				'keyCompromise',
				'cACompromise',
				'affiliationChanged',
				'superseded',
				'cessationOfOperation',
				'certificateHold',
				'privilegeWithdrawn',
				'aACompromise',
			),
		);

		$DistributionPointName = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_CHOICE,
			'children' => array(
				'fullName'                => array(
					'constant' => 0,
					'optional' => true,
					'implicit' => true,
				) + $GeneralNames, // @codingStandardsIgnoreLine.
				'nameRelativeToCRLIssuer' => array(
					'constant' => 1,
					'optional' => true,
					'implicit' => true,
				) + $this->RelativeDistinguishedName, // @codingStandardsIgnoreLine.
			),
		);

		$DistributionPoint = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'distributionPoint' => array(
					'constant' => 0,
					'optional' => true,
					'explicit' => true,
				) + $DistributionPointName, // @codingStandardsIgnoreLine.
				'reasons'           => array(
					'constant' => 1,
					'optional' => true,
					'implicit' => true,
				) + $ReasonFlags, // @codingStandardsIgnoreLine.
				'cRLIssuer'         => array(
					'constant' => 2,
					'optional' => true,
					'implicit' => true,
				) + $GeneralNames, // @codingStandardsIgnoreLine.
			),
		);

		$this->CRLDistributionPoints = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'min'      => 1,
			'max'      => -1,
			'children' => $DistributionPoint, // @codingStandardsIgnoreLine.
		);

		$this->AuthorityKeyIdentifier = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'keyIdentifier'             => array(
					'constant' => 0,
					'optional' => true,
					'implicit' => true,
				) + $this->KeyIdentifier, // @codingStandardsIgnoreLine.
				'authorityCertIssuer'       => array(
					'constant' => 1,
					'optional' => true,
					'implicit' => true,
				) + $GeneralNames, // @codingStandardsIgnoreLine.
				'authorityCertSerialNumber' => array(
					'constant' => 2,
					'optional' => true,
					'implicit' => true,
				) + $CertificateSerialNumber, // @codingStandardsIgnoreLine.
			),
		);

		$PolicyQualifierId = array( 'type' => ASN1::TYPE_OBJECT_IDENTIFIER ); // @codingStandardsIgnoreLine.

		$PolicyQualifierInfo = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'policyQualifierId' => $PolicyQualifierId, // @codingStandardsIgnoreLine.
				'qualifier'         => array( 'type' => ASN1::TYPE_ANY ),
			),
		);

		$CertPolicyId = array( 'type' => ASN1::TYPE_OBJECT_IDENTIFIER ); // @codingStandardsIgnoreLine.

		$PolicyInformation = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'policyIdentifier' => $CertPolicyId, // @codingStandardsIgnoreLine.
				'policyQualifiers' => array(
					'type'     => ASN1::TYPE_SEQUENCE,
					'min'      => 0,
					'max'      => -1,
					'optional' => true,
					'children' => $PolicyQualifierInfo, // @codingStandardsIgnoreLine.
				),
			),
		);

		$this->CertificatePolicies = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'min'      => 1,
			'max'      => -1,
			'children' => $PolicyInformation, // @codingStandardsIgnoreLine.
		);

		$this->PolicyMappings = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'min'      => 1,
			'max'      => -1,
			'children' => array(
				'type'     => ASN1::TYPE_SEQUENCE,
				'children' => array(
					'issuerDomainPolicy'  => $CertPolicyId, // @codingStandardsIgnoreLine.
					'subjectDomainPolicy' => $CertPolicyId, // @codingStandardsIgnoreLine.
				),
			),
		);

		$KeyPurposeId = array( 'type' => ASN1::TYPE_OBJECT_IDENTIFIER ); // @codingStandardsIgnoreLine.

		$this->ExtKeyUsageSyntax = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'min'      => 1,
			'max'      => -1,
			'children' => $KeyPurposeId, // @codingStandardsIgnoreLine.
		);

		$AccessDescription = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'accessMethod'   => array( 'type' => ASN1::TYPE_OBJECT_IDENTIFIER ),
				'accessLocation' => $GeneralName, // @codingStandardsIgnoreLine.
			),
		);

		$this->AuthorityInfoAccessSyntax = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'min'      => 1,
			'max'      => -1,
			'children' => $AccessDescription, // @codingStandardsIgnoreLine.
		);

		$this->SubjectAltName = $GeneralNames; // @codingStandardsIgnoreLine.

		$this->PrivateKeyUsagePeriod = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'notBefore' => array(
					'constant' => 0,
					'optional' => true,
					'implicit' => true,
					'type'     => ASN1::TYPE_GENERALIZED_TIME,
				),
				'notAfter'  => array(
					'constant' => 1,
					'optional' => true,
					'implicit' => true,
					'type'     => ASN1::TYPE_GENERALIZED_TIME,
				),
			),
		);

		$BaseDistance = array( 'type' => ASN1::TYPE_INTEGER ); // @codingStandardsIgnoreLine.

		$GeneralSubtree = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'base'    => $GeneralName, // @codingStandardsIgnoreLine.
				'minimum' => array(
					'constant' => 0,
					'optional' => true,
					'implicit' => true,
					'default'  => new BigInteger( 0 ),
				) + $BaseDistance, // @codingStandardsIgnoreLine.
				'maximum' => array(
					'constant' => 1,
					'optional' => true,
					'implicit' => true,
				) + $BaseDistance, // @codingStandardsIgnoreLine.
			),
		);

		$GeneralSubtrees = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'min'      => 1,
			'max'      => -1,
			'children' => $GeneralSubtree, // @codingStandardsIgnoreLine.
		);

		$this->NameConstraints = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'permittedSubtrees' => array(
					'constant' => 0,
					'optional' => true,
					'implicit' => true,
				) + $GeneralSubtrees, // @codingStandardsIgnoreLine.
				'excludedSubtrees'  => array(
					'constant' => 1,
					'optional' => true,
					'implicit' => true,
				) + $GeneralSubtrees, // @codingStandardsIgnoreLine.
			),
		);

		$this->CPSuri = array( 'type' => ASN1::TYPE_IA5_STRING ); // @codingStandardsIgnoreLine.

		$DisplayText = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_CHOICE,
			'children' => array(
				'ia5String'     => array( 'type' => ASN1::TYPE_IA5_STRING ),
				'visibleString' => array( 'type' => ASN1::TYPE_VISIBLE_STRING ),
				'bmpString'     => array( 'type' => ASN1::TYPE_BMP_STRING ),
				'utf8String'    => array( 'type' => ASN1::TYPE_UTF8_STRING ),
			),
		);

		$NoticeReference = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'organization'  => $DisplayText, // @codingStandardsIgnoreLine.
				'noticeNumbers' => array(
					'type'     => ASN1::TYPE_SEQUENCE,
					'min'      => 1,
					'max'      => 200,
					'children' => array( 'type' => ASN1::TYPE_INTEGER ),
				),
			),
		);

		$this->UserNotice = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'noticeRef'    => array(
					'optional' => true,
					'implicit' => true,
				) + $NoticeReference, // @codingStandardsIgnoreLine.
				'explicitText' => array(
					'optional' => true,
					'implicit' => true,
				) + $DisplayText, // @codingStandardsIgnoreLine.
			),
		);

		$this->netscape_cert_type = array(
			'type'    => ASN1::TYPE_BIT_STRING,
			'mapping' => array(
				'SSLClient',
				'SSLServer',
				'Email',
				'ObjectSigning',
				'Reserved',
				'SSLCA',
				'EmailCA',
				'ObjectSigningCA',
			),
		);

		$this->netscape_comment       = array( 'type' => ASN1::TYPE_IA5_STRING );
		$this->netscape_ca_policy_url = array( 'type' => ASN1::TYPE_IA5_STRING );

		// attribute is used in RFC2986 but we're using the RFC5280 definition .
		$Attribute = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'type' => $AttributeType, // @codingStandardsIgnoreLine.
				'value' => array(
					'type'     => ASN1::TYPE_SET,
					'min'      => 1,
					'max'      => -1,
					'children' => $this->AttributeValue, // @codingStandardsIgnoreLine.
				),
			),
		);

		$this->SubjectDirectoryAttributes = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'min'      => 1,
			'max'      => -1,
			'children' => $Attribute, // @codingStandardsIgnoreLine.
		);

		$Attributes = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SET,
			'min'      => 1,
			'max'      => -1,
			'children' => $Attribute, // @codingStandardsIgnoreLine.
		);

		$CertificationRequestInfo = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'version'       => array(
					'type'    => ASN1::TYPE_INTEGER,
					'mapping' => array( 'v1' ),
				),
				'subject'       => $this->Name, // @codingStandardsIgnoreLine.
				'subjectPKInfo' => $SubjectPublicKeyInfo, // @codingStandardsIgnoreLine.
				'attributes'    => array(
					'constant' => 0,
					'optional' => true,
					'implicit' => true,
				) + $Attributes, // @codingStandardsIgnoreLine.
			),
		);

		$this->CertificationRequest = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'certificationRequestInfo' => $CertificationRequestInfo, // @codingStandardsIgnoreLine.
				'signatureAlgorithm'       => $AlgorithmIdentifier, // @codingStandardsIgnoreLine.
				'signature'                => array( 'type' => ASN1::TYPE_BIT_STRING ),
			),
		);

		$RevokedCertificate = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'userCertificate'    => $CertificateSerialNumber, // @codingStandardsIgnoreLine.
				'revocationDate'     => $Time, // @codingStandardsIgnoreLine.
				'crlEntryExtensions' => array(
					'optional' => true,
				) + $this->Extensions, // @codingStandardsIgnoreLine.
			),
		);

		$TBSCertList = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'version'             => array(
					'optional' => true,
					'default'  => 'v1',
				) + $version,
				'signature'           => $AlgorithmIdentifier, // @codingStandardsIgnoreLine.
				'issuer'              => $this->Name, // @codingStandardsIgnoreLine.
				'thisUpdate'          => $Time, // @codingStandardsIgnoreLine.
				'nextUpdate'          => array(
					'optional' => true,
				) + $Time, // @codingStandardsIgnoreLine.
				'revokedCertificates' => array(
					'type'     => ASN1::TYPE_SEQUENCE,
					'optional' => true,
					'min'      => 0,
					'max'      => -1,
					'children' => $RevokedCertificate, // @codingStandardsIgnoreLine.
				),
				'crlExtensions'       => array(
					'constant' => 0,
					'optional' => true,
					'explicit' => true,
				) + $this->Extensions, // @codingStandardsIgnoreLine.
			),
		);

		$this->CertificateList = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'tbsCertList'        => $TBSCertList, // @codingStandardsIgnoreLine.
				'signatureAlgorithm' => $AlgorithmIdentifier, // @codingStandardsIgnoreLine.
				'signature'          => array( 'type' => ASN1::TYPE_BIT_STRING ),
			),
		);

		$this->CRLNumber = array( 'type' => ASN1::TYPE_INTEGER ); // @codingStandardsIgnoreLine.
		$this->CRLReason = array( 'type' => ASN1::TYPE_ENUMERATED, // @codingStandardsIgnoreLine.
				'mapping'                => array(
				'unspecified',
				'keyCompromise',
				'cACompromise',
				'affiliationChanged',
				'superseded',
				'cessationOfOperation',
				'certificateHold',
				// Value 7 is not used.
				8 => 'removeFromCRL',
				'privilegeWithdrawn',
				'aACompromise',
			),
		);

		$this->IssuingDistributionPoint = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'distributionPoint'          => array(
					'constant' => 0,
					'optional' => true,
					'explicit' => true,
				) + $DistributionPointName, // @codingStandardsIgnoreLine.
				'onlyContainsUserCerts'      => array(
					'type'     => ASN1::TYPE_BOOLEAN,
					'constant' => 1,
					'optional' => true,
					'default'  => false,
					'implicit' => true,
				),
				'onlyContainsCACerts'        => array(
					'type'     => ASN1::TYPE_BOOLEAN,
					'constant' => 2,
					'optional' => true,
					'default'  => false,
					'implicit' => true,
				),
				'onlySomeReasons'            => array(
					'constant' => 3,
					'optional' => true,
					'implicit' => true,
				) + $ReasonFlags, // @codingStandardsIgnoreLine.
				'indirectCRL'                => array(
					'type'     => ASN1::TYPE_BOOLEAN,
					'constant' => 4,
					'optional' => true,
					'default'  => false,
					'implicit' => true,
				),
				'onlyContainsAttributeCerts' => array(
					'type'     => ASN1::TYPE_BOOLEAN,
					'constant' => 5,
					'optional' => true,
					'default'  => false,
					'implicit' => true,
				),
			),
		);

		$this->InvalidityDate    = array( 'type' => ASN1::TYPE_GENERALIZED_TIME ); // @codingStandardsIgnoreLine.
		$this->CertificateIssuer = $GeneralNames; // @codingStandardsIgnoreLine.

		$this->HoldInstructionCode = array( 'type' => ASN1::TYPE_OBJECT_IDENTIFIER ); // @codingStandardsIgnoreLine.

		$PublicKeyAndChallenge = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'spki'      => $SubjectPublicKeyInfo, // @codingStandardsIgnoreLine.
				'challenge' => array( 'type' => ASN1::TYPE_IA5_STRING ),
			),
		);

		$this->SignedPublicKeyAndChallenge = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'children' => array(
				'publicKeyAndChallenge' => $PublicKeyAndChallenge, // @codingStandardsIgnoreLine.
				'signatureAlgorithm'    => $AlgorithmIdentifier, // @codingStandardsIgnoreLine.
				'signature'             => array( 'type' => ASN1::TYPE_BIT_STRING ),
			),
		);

		$this->PostalAddress = array( // @codingStandardsIgnoreLine.
			'type'     => ASN1::TYPE_SEQUENCE,
			'optional' => true,
			'min'      => 1,
			'max'      => -1,
			'children' => $this->DirectoryString, // @codingStandardsIgnoreLine.
		);

		// OIDs from RFC5280 and those RFCs mentioned in RFC5280#section-4.1.1.2 .
		$this->oids = array(
			'1.3.6.1.5.5.7'              => 'id-pkix',
			'1.3.6.1.5.5.7.1'            => 'id-pe',
			'1.3.6.1.5.5.7.2'            => 'id-qt',
			'1.3.6.1.5.5.7.3'            => 'id-kp',
			'1.3.6.1.5.5.7.48'           => 'id-ad',
			'1.3.6.1.5.5.7.2.1'          => 'id-qt-cps',
			'1.3.6.1.5.5.7.2.2'          => 'id-qt-unotice',
			'1.3.6.1.5.5.7.48.1'         => 'id-ad-ocsp',
			'1.3.6.1.5.5.7.48.2'         => 'id-ad-caIssuers',
			'1.3.6.1.5.5.7.48.3'         => 'id-ad-timeStamping',
			'1.3.6.1.5.5.7.48.5'         => 'id-ad-caRepository',
			'2.5.4'                      => 'id-at',
			'2.5.4.41'                   => 'id-at-name',
			'2.5.4.4'                    => 'id-at-surname',
			'2.5.4.42'                   => 'id-at-givenName',
			'2.5.4.43'                   => 'id-at-initials',
			'2.5.4.44'                   => 'id-at-generationQualifier',
			'2.5.4.3'                    => 'id-at-commonName',
			'2.5.4.7'                    => 'id-at-localityName',
			'2.5.4.8'                    => 'id-at-stateOrProvinceName',
			'2.5.4.10'                   => 'id-at-organizationName',
			'2.5.4.11'                   => 'id-at-organizationalUnitName',
			'2.5.4.12'                   => 'id-at-title',
			'2.5.4.13'                   => 'id-at-description',
			'2.5.4.46'                   => 'id-at-dnQualifier',
			'2.5.4.6'                    => 'id-at-countryName',
			'2.5.4.5'                    => 'id-at-serialNumber',
			'2.5.4.65'                   => 'id-at-pseudonym',
			'2.5.4.17'                   => 'id-at-postalCode',
			'2.5.4.9'                    => 'id-at-streetAddress',
			'2.5.4.45'                   => 'id-at-uniqueIdentifier',
			'2.5.4.72'                   => 'id-at-role',
			'2.5.4.16'                   => 'id-at-postalAddress',

			'0.9.2342.19200300.100.1.25' => 'id-domainComponent',
			'1.2.840.113549.1.9'         => 'pkcs-9',
			'1.2.840.113549.1.9.1'       => 'pkcs-9-at-emailAddress',
			'2.5.29'                     => 'id-ce',
			'2.5.29.35'                  => 'id-ce-authorityKeyIdentifier',
			'2.5.29.14'                  => 'id-ce-subjectKeyIdentifier',
			'2.5.29.15'                  => 'id-ce-keyUsage',
			'2.5.29.16'                  => 'id-ce-privateKeyUsagePeriod',
			'2.5.29.32'                  => 'id-ce-certificatePolicies',
			'2.5.29.32.0'                => 'anyPolicy',

			'2.5.29.33'                  => 'id-ce-policyMappings',
			'2.5.29.17'                  => 'id-ce-subjectAltName',
			'2.5.29.18'                  => 'id-ce-issuerAltName',
			'2.5.29.9'                   => 'id-ce-subjectDirectoryAttributes',
			'2.5.29.19'                  => 'id-ce-basicConstraints',
			'2.5.29.30'                  => 'id-ce-nameConstraints',
			'2.5.29.36'                  => 'id-ce-policyConstraints',
			'2.5.29.31'                  => 'id-ce-cRLDistributionPoints',
			'2.5.29.37'                  => 'id-ce-extKeyUsage',
			'2.5.29.37.0'                => 'anyExtendedKeyUsage',
			'1.3.6.1.5.5.7.3.1'          => 'id-kp-serverAuth',
			'1.3.6.1.5.5.7.3.2'          => 'id-kp-clientAuth',
			'1.3.6.1.5.5.7.3.3'          => 'id-kp-codeSigning',
			'1.3.6.1.5.5.7.3.4'          => 'id-kp-emailProtection',
			'1.3.6.1.5.5.7.3.8'          => 'id-kp-timeStamping',
			'1.3.6.1.5.5.7.3.9'          => 'id-kp-OCSPSigning',
			'2.5.29.54'                  => 'id-ce-inhibitAnyPolicy',
			'2.5.29.46'                  => 'id-ce-freshestCRL',
			'1.3.6.1.5.5.7.1.1'          => 'id-pe-authorityInfoAccess',
			'1.3.6.1.5.5.7.1.11'         => 'id-pe-subjectInfoAccess',
			'2.5.29.20'                  => 'id-ce-cRLNumber',
			'2.5.29.28'                  => 'id-ce-issuingDistributionPoint',
			'2.5.29.27'                  => 'id-ce-deltaCRLIndicator',
			'2.5.29.21'                  => 'id-ce-cRLReasons',
			'2.5.29.29'                  => 'id-ce-certificateIssuer',
			'2.5.29.23'                  => 'id-ce-holdInstructionCode',
			'1.2.840.10040.2'            => 'holdInstruction',
			'1.2.840.10040.2.1'          => 'id-holdinstruction-none',
			'1.2.840.10040.2.2'          => 'id-holdinstruction-callissuer',
			'1.2.840.10040.2.3'          => 'id-holdinstruction-reject',
			'2.5.29.24'                  => 'id-ce-invalidityDate',

			'1.2.840.113549.2.2'         => 'md2',
			'1.2.840.113549.2.5'         => 'md5',
			'1.3.14.3.2.26'              => 'id-sha1',
			'1.2.840.10040.4.1'          => 'id-dsa',
			'1.2.840.10040.4.3'          => 'id-dsa-with-sha1',
			'1.2.840.113549.1.1'         => 'pkcs-1',
			'1.2.840.113549.1.1.1'       => 'rsaEncryption',
			'1.2.840.113549.1.1.2'       => 'md2WithRSAEncryption',
			'1.2.840.113549.1.1.4'       => 'md5WithRSAEncryption',
			'1.2.840.113549.1.1.5'       => 'sha1WithRSAEncryption',
			'1.2.840.10046.2.1'          => 'dhpublicnumber',
			'2.16.840.1.101.2.1.1.22'    => 'id-keyExchangeAlgorithm',
			'1.2.840.10045'              => 'ansi-X9-62',
			'1.2.840.10045.4'            => 'id-ecSigType',
			'1.2.840.10045.4.1'          => 'ecdsa-with-SHA1',
			'1.2.840.10045.1'            => 'id-fieldType',
			'1.2.840.10045.1.1'          => 'prime-field',
			'1.2.840.10045.1.2'          => 'characteristic-two-field',
			'1.2.840.10045.1.2.3'        => 'id-characteristic-two-basis',
			'1.2.840.10045.1.2.3.1'      => 'gnBasis',
			'1.2.840.10045.1.2.3.2'      => 'tpBasis',
			'1.2.840.10045.1.2.3.3'      => 'ppBasis',
			'1.2.840.10045.2'            => 'id-publicKeyType',
			'1.2.840.10045.2.1'          => 'id-ecPublicKey',
			'1.2.840.10045.3'            => 'ellipticCurve',
			'1.2.840.10045.3.0'          => 'c-TwoCurve',
			'1.2.840.10045.3.0.1'        => 'c2pnb163v1',
			'1.2.840.10045.3.0.2'        => 'c2pnb163v2',
			'1.2.840.10045.3.0.3'        => 'c2pnb163v3',
			'1.2.840.10045.3.0.4'        => 'c2pnb176w1',
			'1.2.840.10045.3.0.5'        => 'c2pnb191v1',
			'1.2.840.10045.3.0.6'        => 'c2pnb191v2',
			'1.2.840.10045.3.0.7'        => 'c2pnb191v3',
			'1.2.840.10045.3.0.8'        => 'c2pnb191v4',
			'1.2.840.10045.3.0.9'        => 'c2pnb191v5',
			'1.2.840.10045.3.0.10'       => 'c2pnb208w1',
			'1.2.840.10045.3.0.11'       => 'c2pnb239v1',
			'1.2.840.10045.3.0.12'       => 'c2pnb239v2',
			'1.2.840.10045.3.0.13'       => 'c2pnb239v3',
			'1.2.840.10045.3.0.14'       => 'c2pnb239v4',
			'1.2.840.10045.3.0.15'       => 'c2pnb239v5',
			'1.2.840.10045.3.0.16'       => 'c2pnb272w1',
			'1.2.840.10045.3.0.17'       => 'c2pnb304w1',
			'1.2.840.10045.3.0.18'       => 'c2pnb359v1',
			'1.2.840.10045.3.0.19'       => 'c2pnb368w1',
			'1.2.840.10045.3.0.20'       => 'c2pnb431r1',
			'1.2.840.10045.3.1'          => 'primeCurve',
			'1.2.840.10045.3.1.1'        => 'prime192v1',
			'1.2.840.10045.3.1.2'        => 'prime192v2',
			'1.2.840.10045.3.1.3'        => 'prime192v3',
			'1.2.840.10045.3.1.4'        => 'prime239v1',
			'1.2.840.10045.3.1.5'        => 'prime239v2',
			'1.2.840.10045.3.1.6'        => 'prime239v3',
			'1.2.840.10045.3.1.7'        => 'prime256v1',
			'1.2.840.113549.1.1.7'       => 'id-RSAES-OAEP',
			'1.2.840.113549.1.1.9'       => 'id-pSpecified',
			'1.2.840.113549.1.1.10'      => 'id-RSASSA-PSS',
			'1.2.840.113549.1.1.8'       => 'id-mgf1',
			'1.2.840.113549.1.1.14'      => 'sha224WithRSAEncryption',
			'1.2.840.113549.1.1.11'      => 'sha256WithRSAEncryption',
			'1.2.840.113549.1.1.12'      => 'sha384WithRSAEncryption',
			'1.2.840.113549.1.1.13'      => 'sha512WithRSAEncryption',
			'2.16.840.1.101.3.4.2.4'     => 'id-sha224',
			'2.16.840.1.101.3.4.2.1'     => 'id-sha256',
			'2.16.840.1.101.3.4.2.2'     => 'id-sha384',
			'2.16.840.1.101.3.4.2.3'     => 'id-sha512',
			'1.2.643.2.2.4'              => 'id-GostR3411-94-with-GostR3410-94',
			'1.2.643.2.2.3'              => 'id-GostR3411-94-with-GostR3410-2001',
			'1.2.643.2.2.20'             => 'id-GostR3410-2001',
			'1.2.643.2.2.19'             => 'id-GostR3410-94',
			// Netscape Object Identifiers from "Netscape Certificate Extensions" .
			'2.16.840.1.113730'          => 'netscape',
			'2.16.840.1.113730.1'        => 'netscape-cert-extension',
			'2.16.840.1.113730.1.1'      => 'netscape-cert-type',
			'2.16.840.1.113730.1.13'     => 'netscape-comment',
			'2.16.840.1.113730.1.8'      => 'netscape-ca-policy-url',
			// the following are X.509 extensions not supported by phpseclib .
			'1.3.6.1.5.5.7.1.12'         => 'id-pe-logotype',
			'1.2.840.113533.7.65.0'      => 'entrustVersInfo',
			'2.16.840.1.113733.1.6.9'    => 'verisignPrivate',
			// for Certificate Signing Requests
			// see http://tools.ietf.org/html/rfc2985 .
			'1.2.840.113549.1.9.2'       => 'pkcs-9-at-unstructuredName', // PKCS #9 unstructured name.
			'1.2.840.113549.1.9.7'       => 'pkcs-9-at-challengePassword', // Challenge password for certificate revocations.
			'1.2.840.113549.1.9.14'      => 'pkcs-9-at-extensionRequest', // Certificate extension request.
		);
	}

	/**
	 * Load X.509 certificate
	 *
	 * Returns an associative array describing the X.509 cert or a false if the cert failed to load
	 *
	 * @param string $cert .
	 * @param int    $mode .
	 * @access public
	 * @return mixed
	 */
	public function loadX509( $cert, $mode = self::FORMAT_AUTO_DETECT ) { // @codingStandardsIgnoreLine.
		if ( is_array( $cert ) && isset( $cert['tbsCertificate'] ) ) {
			unset( $this->currentCert ); // @codingStandardsIgnoreLine.
			unset( $this->currentKeyIdentifier ); // @codingStandardsIgnoreLine.
			$this->dn = $cert['tbsCertificate']['subject'];
			if ( ! isset( $this->dn ) ) {
				return false;
			}
			$this->currentCert = $cert; // @codingStandardsIgnoreLine.
			$currentKeyIdentifier       = $this->getExtension( 'id-ce-subjectKeyIdentifier' ); // @codingStandardsIgnoreLine.
			$this->currentKeyIdentifier = is_string( $currentKeyIdentifier ) ? $currentKeyIdentifier : null; // @codingStandardsIgnoreLine.
			unset( $this->signatureSubject ); // @codingStandardsIgnoreLine.
			return $cert;
		}

		$asn1 = new ASN1();

		if ( self::FORMAT_DER != $mode ) { // WPCS:Loose comparison ok .
			$newcert = $this->_extractBER( $cert );
			if ( self::FORMAT_PEM == $mode && $cert == $newcert ) { // WPCS:Loose comparison ok .
					return false;
			}
			$cert = $newcert;
		}

		if ( false === $cert ) {
				$this->currentCert = false; // @codingStandardsIgnoreLine.
				return false;
		}

		$asn1->loadOIDs( $this->oids );
		$decoded = $asn1->decodeBER( $cert );

		if ( ! empty( $decoded ) ) {
			$x509 = $asn1->asn1map($decoded[0], $this->Certificate); // @codingStandardsIgnoreLine.
		}
		if ( ! isset( $x509 ) || false === $x509 ) {
				$this->currentCert = false; // @codingStandardsIgnoreLine.
				return false;
		}

		$this->signatureSubject = substr( $cert, $decoded[0]['content'][0]['start'], $decoded[0]['content'][0]['length'] ); // @codingStandardsIgnoreLine.

		if ( $this->_isSubArrayValid( $x509, 'tbsCertificate/extensions' ) ) {
				$this->_mapInExtensions( $x509, 'tbsCertificate/extensions', $asn1 );
		}
		$this->_mapInDNs( $x509, 'tbsCertificate/issuer/rdnSequence', $asn1 );
		$this->_mapInDNs( $x509, 'tbsCertificate/subject/rdnSequence', $asn1 );

		$key = &$x509['tbsCertificate']['subjectPublicKeyInfo']['subjectPublicKey'];
		$key = $this->_reformatKey( $x509['tbsCertificate']['subjectPublicKeyInfo']['algorithm']['algorithm'], $key );

		$this->currentCert = $x509; // @codingStandardsIgnoreLine.
		$this->dn          = $x509['tbsCertificate']['subject'];

		$currentKeyIdentifier       = $this->getExtension( 'id-ce-subjectKeyIdentifier' ); // @codingStandardsIgnoreLine.
		$this->currentKeyIdentifier = is_string( $currentKeyIdentifier ) ? $currentKeyIdentifier : null; // @codingStandardsIgnoreLine.
		return $x509;
	}

	/**
	 * Save X.509 certificate
	 *
	 * @param array $cert .
	 * @param int   $format optional .
	 * @access public
	 * @return string
	 */
	public function saveX509( $cert, $format = self::FORMAT_PEM ) { // @codingStandardsIgnoreLine.
		if ( ! is_array( $cert ) || ! isset( $cert['tbsCertificate'] ) ) {
				return false;
		}

		switch ( true ) {
			// "case !$a: case !$b: break; default: whatever();" is the same thing as "if ($a && $b) whatever()"
			case ! ( $algorithm = $this->_subArray( $cert, 'tbsCertificate/subjectPublicKeyInfo/algorithm/algorithm' ) ): // @codingStandardsIgnoreLine.
			case is_object( $cert['tbsCertificate']['subjectPublicKeyInfo']['subjectPublicKey'] ):
				break;
			default:
				switch ( $algorithm ) {
					case 'rsaEncryption':
						$cert['tbsCertificate']['subjectPublicKeyInfo']['subjectPublicKey']
									= base64_encode( "\0" . base64_decode( preg_replace( '#-.+-|[\r\n]#', '', $cert['tbsCertificate']['subjectPublicKeyInfo']['subjectPublicKey'] ) ) );

						$cert['tbsCertificate']['subjectPublicKeyInfo']['algorithm']['parameters'] = null;
						// https://tools.ietf.org/html/rfc3279#section-2.2.1 .
						$cert['signatureAlgorithm']['parameters']          = null;
						$cert['tbsCertificate']['signature']['parameters'] = null;
				}
		}

		$asn1 = new ASN1();
		$asn1->loadOIDs( $this->oids );

		$filters          = array();
		$type_utf8_string = array( 'type' => ASN1::TYPE_UTF8_STRING );
		$filters['tbsCertificate']['signature']['parameters']                              = $type_utf8_string;
		$filters['tbsCertificate']['signature']['issuer']['rdnSequence']['value']          = $type_utf8_string;
		$filters['tbsCertificate']['issuer']['rdnSequence']['value']                       = $type_utf8_string;
		$filters['tbsCertificate']['subject']['rdnSequence']['value']                      = $type_utf8_string;
		$filters['tbsCertificate']['subjectPublicKeyInfo']['algorithm']['parameters']      = $type_utf8_string;
		$filters['signatureAlgorithm']['parameters']                                       = $type_utf8_string;
		$filters['authorityCertIssuer']['directoryName']['rdnSequence']['value']           = $type_utf8_string;
		$filters['distributionPoint']['fullName']['directoryName']['rdnSequence']['value'] = $type_utf8_string;
		$filters['directoryName']['rdnSequence']['value']                                  = $type_utf8_string;

		$filters['policyQualifiers']['qualifier']
				= array( 'type' => ASN1::TYPE_IA5_STRING );

		$asn1->loadFilters( $filters );

		$this->_mapOutExtensions( $cert, 'tbsCertificate/extensions', $asn1 );
		$this->_mapOutDNs( $cert, 'tbsCertificate/issuer/rdnSequence', $asn1 );
		$this->_mapOutDNs( $cert, 'tbsCertificate/subject/rdnSequence', $asn1 );

		$cert = $asn1->encodeDER( $cert, $this->Certificate ); // @codingStandardsIgnoreLine.

		switch ( $format ) {
			case self::FORMAT_DER:
				return $cert;
			default:
				return "-----BEGIN CERTIFICATE-----\r\n" . chunk_split( base64_encode( $cert ), 64 ) . '-----END CERTIFICATE-----';
		}
	}

	/**
	 * Map extension values from octet string to extension-specific internal
	 *   format.
	 *
	 * @param array ref $root .
	 * @param string    $path .
	 * @param object    $asn1 .
	 * @access private
	 */
	private function _mapInExtensions( &$root, $path, $asn1 ) { // @codingStandardsIgnoreLine.
		$extensions = &$this->_subArrayUnchecked( $root, $path );
		if ( $extensions ) {
			for ( $i = 0; $i < count( $extensions ); $i++ ) { // @codingStandardsIgnoreLine.
				$id      = $extensions[ $i ]['extnId'];
				$value   = &$extensions[ $i ]['extnValue'];
				$value   = base64_decode( $value );
				$decoded = $asn1->decodeBER( $value );
				$map     = $this->_getMapping( $id );
				if ( ! is_bool( $map ) ) {
					$mapped = $asn1->asn1map( $decoded[0], $map, array( 'iPAddress' => array( $this, '_decodeIP' ) ) );
					$value  = false === $mapped ? $decoded[0] : $mapped;
					if ( 'id-ce-certificatePolicies' == $id ) { // WPCS:Loose comparison ok.
						for ( $j = 0; $j < count( $value ); $j++ ) { // @codingStandardsIgnoreLine.
							if ( ! isset( $value[ $j ]['policyQualifiers'] ) ) {
									continue;
							}
							for ( $k = 0; $k < count( $value[ $j ]['policyQualifiers'] ); $k++ ) { // @codingStandardsIgnoreLine.
								$subid    = $value[ $j ]['policyQualifiers'][ $k ]['policyQualifierId'];
								$map      = $this->_getMapping( $subid );
								$subvalue = &$value[ $j ]['policyQualifiers'][ $k ]['qualifier'];
								if ( false !== $map ) {
									$decoded  = $asn1->decodeBER( $subvalue );
									$mapped   = $asn1->asn1map( $decoded[0], $map );
									$subvalue = false === $mapped ? $decoded[0] : $mapped;
								}
							}
						}
					}
				} else {
					$value = base64_encode( $value );
				}
			}
		}
	}

	/**
	 * Map extension values from extension-specific internal format to
	 *   octet string.
	 *
	 * @param array ref $root .
	 * @param string    $path .
	 * @param object    $asn1 .
	 * @access private
	 */
	private function _mapOutExtensions( &$root, $path, $asn1 ) { // @codingStandardsIgnoreLine.
		$extensions = &$this->_subArray( $root, $path );
		if ( is_array( $extensions ) ) {
			$size = count( $extensions );
			for ( $i = 0; $i < $size; $i++ ) {
				if ( $extensions[ $i ] instanceof Element ) {
					continue;
				}
				$id    = $extensions[ $i ]['extnId'];
				$value = &$extensions[ $i ]['extnValue'];
				switch ( $id ) {
					case 'id-ce-certificatePolicies':
						for ( $j = 0; $j < count( $value ); $j++ ) { // @codingStandardsIgnoreLine.
							if ( ! isset( $value[ $j ]['policyQualifiers'] ) ) {
									continue;
							}
							for ( $k = 0; $k < count( $value[ $j ]['policyQualifiers'] ); $k++ ) { // @codingStandardsIgnoreLine.
								$subid    = $value[ $j ]['policyQualifiers'][ $k ]['policyQualifierId'];
								$map      = $this->_getMapping( $subid );
								$subvalue = &$value[ $j ]['policyQualifiers'][ $k ]['qualifier'];
								if ( false !== $map ) {
									// by default \phpseclib\File\ASN1 will try to render qualifier as a \phpseclib\File\ASN1::TYPE_IA5_STRING since it's
									// actual type is \phpseclib\File\ASN1::TYPE_ANY .
									$subvalue = new Element( $asn1->encodeDER( $subvalue, $map ) );
								}
							}
						}
						break;
					case 'id-ce-authorityKeyIdentifier': // use 00 as the serial number instead of an empty string .
						if ( isset( $value['authorityCertSerialNumber'] ) ) {
							if ( $value['authorityCertSerialNumber']->toBytes() == '' ) { // WPCS:Loose comparison ok.
								$temp                               = chr( ( ASN1::CLASS_CONTEXT_SPECIFIC << 6 ) | 2 ) . "\1\0";
								$value['authorityCertSerialNumber'] = new Element( $temp );
							}
						}
				}
				$map = $this->_getMapping( $id );
				if ( is_bool( $map ) ) {
					if ( ! $map ) {
						user_error( $id . ' is not a currently supported extension' ); // @codingStandardsIgnoreLine.
						unset( $extensions[ $i ] );
					}
				} else {
					$temp  = $asn1->encodeDER( $value, $map, array( 'iPAddress' => array( $this, '_encodeIP' ) ) );
					$value = base64_encode( $temp );
				}
			}
		}
	}

	/**
	 * Map attribute values from ANY type to attribute-specific internal
	 *   format.
	 *
	 * @param array ref $root .
	 * @param string    $path .
	 * @param object    $asn1 .
	 * @access private
	 */
	private function _mapInAttributes( &$root, $path, $asn1 ) { // @codingStandardsIgnoreLine.
		$attributes = &$this->_subArray( $root, $path );
		if ( is_array( $attributes ) ) {
			for ( $i = 0; $i < count( $attributes ); $i++ ) { // @codingStandardsIgnoreLine.
				$id  = $attributes[ $i ]['type'];
				$map = $this->_getMapping( $id );
				if ( is_array( $attributes[ $i ]['value'] ) ) {
					$values = &$attributes[ $i ]['value'];
					for ( $j = 0; $j < count( $values ); $j++ ) { // @codingStandardsIgnoreLine.
						$value   = $asn1->encodeDER( $values[ $j ], $this->AttributeValue ); // @codingStandardsIgnoreLine.
						$decoded = $asn1->decodeBER( $value );
						if ( ! is_bool( $map ) ) {
							$mapped = $asn1->asn1map( $decoded[0], $map );
							if ( false !== $mapped ) {
								$values[ $j ] = $mapped;
							}
							if ( 'pkcs-9-at-extensionRequest' == $id && $this->_isSubArrayValid( $values, $j ) ) { // WPCS:Loose comparison ok.
								$this->_mapInExtensions( $values, $j, $asn1 );
							}
						} elseif ( $map ) {
							$values[ $j ] = base64_encode( $value );
						}
					}
				}
			}
		}
	}

	/**
	 * Map attribute values from attribute-specific internal format to
	 *   ANY type.
	 *
	 * @param array ref $root .
	 * @param string    $path .
	 * @param object    $asn1 .
	 * @access private
	 */
	private function _mapOutAttributes( &$root, $path, $asn1 ) { // @codingStandardsIgnoreLine.
		$attributes = &$this->_subArray( $root, $path );
		if ( is_array( $attributes ) ) {
			$size = count( $attributes );
			for ( $i = 0; $i < $size; $i++ ) {
				$id  = $attributes[ $i ]['type'];
				$map = $this->_getMapping( $id );
				if ( false === $map ) {
						user_error( $id . ' is not a currently supported attribute', E_USER_NOTICE ); // @codingStandardsIgnoreLine.
						unset( $attributes[ $i ] );
				} elseif ( is_array( $attributes[ $i ]['value'] ) ) {
					$values = &$attributes[ $i ]['value'];
					for ( $j = 0; $j < count( $values ); $j++ ) { // @codingStandardsIgnoreLine.
						switch ( $id ) {
							case 'pkcs-9-at-extensionRequest':
								$this->_mapOutExtensions( $values, $j, $asn1 );
								break;
						}
						if ( ! is_bool( $map ) ) {
							$temp         = $asn1->encodeDER( $values[ $j ], $map );
							$decoded      = $asn1->decodeBER( $temp );
							$values[ $j ] = $asn1->asn1map( $decoded[0], $this->AttributeValue ); // @codingStandardsIgnoreLine.
						}
					}
				}
			}
		}
	}

	/**
	 * Map DN values from ANY type to DN-specific internal
	 *   format.
	 *
	 * @param array ref $root .
	 * @param string    $path .
	 * @param object    $asn1 .
	 * @access private
	 */
	private function _mapInDNs( &$root, $path, $asn1 ) { // @codingStandardsIgnoreLine.
		$dns = &$this->_subArray( $root, $path );
		if ( is_array( $dns ) ) {
			for ( $i = 0; $i < count( $dns ); $i++ ) { // @codingStandardsIgnoreLine.
				for ( $j = 0; $j < count( $dns[ $i ] ); $j++ ) { // @codingStandardsIgnoreLine.
					$type  = $dns[ $i ][ $j ]['type'];
					$value = &$dns[ $i ][ $j ]['value'];
					if ( is_object( $value ) && $value instanceof Element ) {
						$map = $this->_getMapping( $type );
						if ( ! is_bool( $map ) ) {
							$decoded = $asn1->decodeBER( $value );
							$value   = $asn1->asn1map( $decoded[0], $map );
						}
					}
				}
			}
		}
	}

	/**
	 * Map DN values from DN-specific internal format to
	 *   ANY type.
	 *
	 * @param array ref $root .
	 * @param string    $path .
	 * @param object    $asn1 .
	 * @access private
	 */
	private function _mapOutDNs( &$root, $path, $asn1 ) { // @codingStandardsIgnoreLine.
		$dns = &$this->_subArray( $root, $path );
		if ( is_array( $dns ) ) {
			$size = count( $dns );
			for ( $i = 0; $i < $size; $i++ ) {
				for ( $j = 0; $j < count( $dns[ $i ] ); $j++ ) { // @codingStandardsIgnoreLine.
					$type  = $dns[ $i ][ $j ]['type'];
					$value = &$dns[ $i ][ $j ]['value'];
					if ( is_object( $value ) && $value instanceof Element ) {
						continue;
					}
					$map = $this->_getMapping( $type );
					if ( ! is_bool( $map ) ) {
						$value = new Element( $asn1->encodeDER( $value, $map ) );
					}
				}
			}
		}
	}

	/**
	 * Associate an extension ID to an extension mapping
	 *
	 * @param string $extnId .
	 * @access private
	 * @return mixed
	 */
	private function _getMapping( $extnId ) { // @codingStandardsIgnoreLine.
		if ( ! is_string( $extnId ) ) { // @codingStandardsIgnoreLine.
			return true;
		}
		switch ( $extnId ) { // @codingStandardsIgnoreLine.
			case 'id-ce-keyUsage':
				return $this->KeyUsage; // @codingStandardsIgnoreLine.
			case 'id-ce-basicConstraints':
				return $this->BasicConstraints; // @codingStandardsIgnoreLine.
			case 'id-ce-subjectKeyIdentifier':
				return $this->KeyIdentifier; // @codingStandardsIgnoreLine.
			case 'id-ce-cRLDistributionPoints':
				return $this->CRLDistributionPoints; // @codingStandardsIgnoreLine.
			case 'id-ce-authorityKeyIdentifier':
				return $this->AuthorityKeyIdentifier; // @codingStandardsIgnoreLine.
			case 'id-ce-certificatePolicies':
				return $this->CertificatePolicies; // @codingStandardsIgnoreLine.
			case 'id-ce-extKeyUsage':
				return $this->ExtKeyUsageSyntax; // @codingStandardsIgnoreLine.
			case 'id-pe-authorityInfoAccess':
				return $this->AuthorityInfoAccessSyntax; // @codingStandardsIgnoreLine.
			case 'id-ce-subjectAltName':
				return $this->SubjectAltName; // @codingStandardsIgnoreLine.
			case 'id-ce-subjectDirectoryAttributes':
				return $this->SubjectDirectoryAttributes; // @codingStandardsIgnoreLine.
			case 'id-ce-privateKeyUsagePeriod':
				return $this->PrivateKeyUsagePeriod; // @codingStandardsIgnoreLine.
			case 'id-ce-issuerAltName':
				return $this->IssuerAltName; // @codingStandardsIgnoreLine.
			case 'id-ce-policyMappings':
				return $this->PolicyMappings; // @codingStandardsIgnoreLine.
			case 'id-ce-nameConstraints':
				return $this->NameConstraints; // @codingStandardsIgnoreLine.
			case 'netscape-cert-type':
				return $this->netscape_cert_type;
			case 'netscape-comment':
				return $this->netscape_comment;
			case 'netscape-ca-policy-url':
				return $this->netscape_ca_policy_url;
			case 'id-qt-unotice':
				return $this->UserNotice; // @codingStandardsIgnoreLine.
			case 'id-pe-logotype':
			case 'entrustVersInfo':
			case '1.3.6.1.4.1.311.20.2': // szOID_ENROLL_CERTTYPE_EXTENSION .
			case '1.3.6.1.4.1.311.21.1': // szOID_CERTSRV_CA_VERSION .
			case '2.23.42.7.0': // id-set-hashedRootKey .
			case '1.3.6.1.4.1.11129.2.4.2':
				return true;
			// CSR attributes .
			case 'pkcs-9-at-unstructuredName':
				return $this->PKCS9String; // @codingStandardsIgnoreLine.
			case 'pkcs-9-at-challengePassword':
				return $this->DirectoryString; // @codingStandardsIgnoreLine.
			case 'pkcs-9-at-extensionRequest':
				return $this->Extensions; // @codingStandardsIgnoreLine.
			// CRL extensions.
			case 'id-ce-cRLNumber':
				return $this->CRLNumber; // @codingStandardsIgnoreLine.
			case 'id-ce-deltaCRLIndicator':
				return $this->CRLNumber; // @codingStandardsIgnoreLine.
			case 'id-ce-issuingDistributionPoint':
				return $this->IssuingDistributionPoint; // @codingStandardsIgnoreLine.
			case 'id-ce-freshestCRL':
				return $this->CRLDistributionPoints; // @codingStandardsIgnoreLine.
			case 'id-ce-cRLReasons':
				return $this->CRLReason; // @codingStandardsIgnoreLine.
			case 'id-ce-invalidityDate':
				return $this->InvalidityDate; // @codingStandardsIgnoreLine.
			case 'id-ce-certificateIssuer':
				return $this->CertificateIssuer; // @codingStandardsIgnoreLine.
			case 'id-ce-holdInstructionCode':
				return $this->HoldInstructionCode; // @codingStandardsIgnoreLine.
			case 'id-at-postalAddress':
				return $this->PostalAddress; // @codingStandardsIgnoreLine.
		}
		return false;
	}

	/**
	 * Load an X.509 certificate as a certificate authority
	 *
	 * @param string $cert .
	 * @access public
	 * @return bool
	 */
	public function loadCA( $cert ) { // @codingStandardsIgnoreLine.
		$olddn      = $this->dn;
		$oldcert    = $this->currentCert; // @codingStandardsIgnoreLine.
		$oldsigsubj = $this->signatureSubject; // @codingStandardsIgnoreLine.
		$oldkeyid   = $this->currentKeyIdentifier; // @codingStandardsIgnoreLine.
		$cert       = $this->loadX509( $cert );
		if ( ! $cert ) {
			$this->dn                   = $olddn;
			$this->currentCert          = $oldcert; // @codingStandardsIgnoreLine.
			$this->signatureSubject     = $oldsigsubj; // @codingStandardsIgnoreLine.
			$this->currentKeyIdentifier = $oldkeyid; // @codingStandardsIgnoreLine.
			return false;
		}
		$this->CAs[]            = $cert; // @codingStandardsIgnoreLine.
		$this->dn               = $olddn;
		$this->currentCert      = $oldcert; // @codingStandardsIgnoreLine.
		$this->signatureSubject = $oldsigsubj; // @codingStandardsIgnoreLine.

			return true;
	}

	/**
	 * Validate an X.509 certificate against a URL
	 *
	 * From RFC2818 "HTTP over TLS":
	 *
	 * Matching is performed using the matching rules specified by
	 * [RFC2459].  If more than one identity of a given type is present in
	 * the certificate (e.g., more than one dNSName name, a match in any one
	 * of the set is considered acceptable.) Names may contain the wildcard
	 * character * which is considered to match any single domain name
	 * component or component fragment. E.g., *.a.com matches foo.a.com but
	 * not bar.foo.a.com. f*.com matches foo.com but not bar.com.
	 *
	 * @param string $url .
	 * @access public
	 * @return bool
	 */
	public function validateURL( $url ) { // @codingStandardsIgnoreLine.
		if ( ! is_array( $this->currentCert ) || ! isset( $this->currentCert['tbsCertificate'] ) ) { // @codingStandardsIgnoreLine.
				return false;
		}
		$components = parse_url( $url ); // @codingStandardsIgnoreLine.
		if ( ! isset( $components['host'] ) ) {
				return false;
		}
		if ( $names = $this->getExtension( 'id-ce-subjectAltName' ) ) { // @codingStandardsIgnoreLine.
			foreach ( $names as $key => $value ) {
				$value = str_replace( array( '.', '*' ), array( '\.', '[^.]*' ), $value );
				switch ( $key ) {
					case 'dNSName':
						if ( preg_match( '#^' . $value . '$#', $components['host'] ) ) {
								return true;
						}
						break;
					case 'iPAddress':
						if ( preg_match( '#(?:\d{1-3}\.){4}#', $components['host'] . '.' ) && preg_match( '#^' . $value . '$#', $components['host'] ) ) {
							return true;
						}
				}
			}
			return false;
		}
		if ( $value = $this->getDNProp( 'id-at-commonName' ) ) { // @codingStandardsIgnoreLine.
			$value = str_replace( array( '.', '*' ), array( '\.', '[^.]*' ), $value[0] );
			return preg_match( '#^' . $value . '$#', $components['host'] );
		}
		return false;
	}

	/**
	 * Validate a date
	 *
	 * If $date isn't defined it is assumed to be the current date.
	 *
	 * @param int $date optional .
	 * @access public
	 */
	public function validateDate( $date = null ) { // @codingStandardsIgnoreLine.
		if ( ! is_array( $this->currentCert ) || ! isset( $this->currentCert['tbsCertificate'] ) ) { // @codingStandardsIgnoreLine.
			return false;
		}
		if ( ! isset( $date ) ) {
				$date = time();
		}
		$notBefore = $this->currentCert['tbsCertificate']['validity']['notBefore']; // @codingStandardsIgnoreLine.
		$notBefore = isset( $notBefore['generalTime'] ) ? $notBefore['generalTime'] : $notBefore['utcTime']; // @codingStandardsIgnoreLine.
		$notAfter = $this->currentCert['tbsCertificate']['validity']['notAfter']; // @codingStandardsIgnoreLine.
		$notAfter = isset( $notAfter['generalTime'] ) ? $notAfter['generalTime'] : $notAfter['utcTime']; // @codingStandardsIgnoreLine.
		switch ( true ) {
			case $date < @strtotime( $notBefore ): // @codingStandardsIgnoreLine.
			case $date > @strtotime( $notAfter ): // @codingStandardsIgnoreLine.
				return false;
		}
		return true;
	}

	/**
	 * Validate a signature
	 *
	 * Works on X.509 certs, CSR's and CRL's.
	 * Returns true if the signature is verified, false if it is not correct or null on error
	 *
	 * By default returns false for self-signed certs. Call validateSignature(false) to make this support
	 * self-signed.
	 *
	 * The behavior of this function is inspired by {@link http://php.net/openssl-verify openssl_verify}.
	 *
	 * @param bool $caonly optional .
	 * @access public
	 * @return mixed
	 */
	public function validateSignature( $caonly = true ) { // @codingStandardsIgnoreLine.
		if ( ! is_array( $this->currentCert ) || ! isset( $this->signatureSubject ) ) { // @codingStandardsIgnoreLine.
			return null;
		}
		switch ( true ) {
			case isset( $this->currentCert['tbsCertificate'] ): // @codingStandardsIgnoreLine.
				switch ( true ) {
					case ! defined( 'FILE_X509_IGNORE_TYPE' ) && $this->currentCert['tbsCertificate']['issuer'] === $this->currentCert['tbsCertificate']['subject']: // @codingStandardsIgnoreLine.
					case defined( 'FILE_X509_IGNORE_TYPE' ) && $this->getIssuerDN( self::DN_STRING ) === $this->getDN( self::DN_STRING ):
						$authorityKey = $this->getExtension( 'id-ce-authorityKeyIdentifier' ); // @codingStandardsIgnoreLine.
						$subjectKeyID = $this->getExtension( 'id-ce-subjectKeyIdentifier' ); // @codingStandardsIgnoreLine.
						switch ( true ) {
							case ! is_array( $authorityKey ): // @codingStandardsIgnoreLine.
							case is_array( $authorityKey ) && isset( $authorityKey['keyIdentifier'] ) && $authorityKey['keyIdentifier'] === $subjectKeyID: // @codingStandardsIgnoreLine.
								$signingCert = $this->currentCert; // @codingStandardsIgnoreLine.
						}
				}
				if ( ! empty( $this->CAs ) ) { // @codingStandardsIgnoreLine.
					for ( $i = 0; $i < count( $this->CAs ); $i++ ) { // @codingStandardsIgnoreLine.
						// even if the cert is a self-signed one we still want to see if it's a CA;
						// if not, we'll conditionally return an error .
						$ca = $this->CAs[ $i ]; // @codingStandardsIgnoreLine.
						switch ( true ) {
							case ! defined( 'FILE_X509_IGNORE_TYPE' ) && $this->currentCert['tbsCertificate']['issuer'] === $ca['tbsCertificate']['subject']: // @codingStandardsIgnoreLine.
							case defined( 'FILE_X509_IGNORE_TYPE' ) && $this->getDN( self::DN_STRING, $this->currentCert['tbsCertificate']['issuer'] ) === $this->getDN( self::DN_STRING, $ca['tbsCertificate']['subject'] ): // @codingStandardsIgnoreLine.
								$authorityKey = $this->getExtension( 'id-ce-authorityKeyIdentifier' ); // @codingStandardsIgnoreLine.
								$subjectKeyID = $this->getExtension( 'id-ce-subjectKeyIdentifier', $ca ); // @codingStandardsIgnoreLine.
								switch ( true ) {
									case ! is_array( $authorityKey ): // @codingStandardsIgnoreLine.
									case is_array( $authorityKey ) && isset( $authorityKey['keyIdentifier'] ) && $authorityKey['keyIdentifier'] === $subjectKeyID: // @codingStandardsIgnoreLine.
										$signingCert = $ca; // @codingStandardsIgnoreLine.
										break 3;
								}
						}
					}
					if ( count( $this->CAs ) == $i && $caonly ) { // @codingStandardsIgnoreLine.
						return false;
					}
				} elseif ( ! isset( $signingCert ) || $caonly ) { // @codingStandardsIgnoreLine.
						return false;
				}
				return $this->_validateSignature(
					$signingCert['tbsCertificate']['subjectPublicKeyInfo']['algorithm']['algorithm'], // @codingStandardsIgnoreLine.
					$signingCert['tbsCertificate']['subjectPublicKeyInfo']['subjectPublicKey'], // @codingStandardsIgnoreLine.
					$this->currentCert['signatureAlgorithm']['algorithm'], // @codingStandardsIgnoreLine.
					substr( base64_decode( $this->currentCert['signature'] ), 1 ), // @codingStandardsIgnoreLine.
					$this->signatureSubject // @codingStandardsIgnoreLine.
				);
			case isset( $this->currentCert['certificationRequestInfo'] ): // @codingStandardsIgnoreLine.
				return $this->_validateSignature(
					$this->currentCert['certificationRequestInfo']['subjectPKInfo']['algorithm']['algorithm'], // @codingStandardsIgnoreLine.
					$this->currentCert['certificationRequestInfo']['subjectPKInfo']['subjectPublicKey'], // @codingStandardsIgnoreLine.
					$this->currentCert['signatureAlgorithm']['algorithm'], // @codingStandardsIgnoreLine.
					substr( base64_decode( $this->currentCert['signature'] ), 1 ), // @codingStandardsIgnoreLine.
					$this->signatureSubject // @codingStandardsIgnoreLine.
				);
			case isset( $this->currentCert['publicKeyAndChallenge'] ): // @codingStandardsIgnoreLine.
				return $this->_validateSignature(
					$this->currentCert['publicKeyAndChallenge']['spki']['algorithm']['algorithm'], // @codingStandardsIgnoreLine.
					$this->currentCert['publicKeyAndChallenge']['spki']['subjectPublicKey'], // @codingStandardsIgnoreLine.
					$this->currentCert['signatureAlgorithm']['algorithm'], // @codingStandardsIgnoreLine.
					substr( base64_decode( $this->currentCert['signature'] ), 1 ), // @codingStandardsIgnoreLine.
					$this->signatureSubject // @codingStandardsIgnoreLine.
				);
			case isset( $this->currentCert['tbsCertList'] ): // @codingStandardsIgnoreLine.
				if ( ! empty( $this->CAs ) ) { // @codingStandardsIgnoreLine.
					for ( $i = 0; $i < count( $this->CAs ); $i++ ) { // @codingStandardsIgnoreLine.
						$ca = $this->CAs[ $i ]; // @codingStandardsIgnoreLine.
						switch ( true ) {
							case ! defined( 'FILE_X509_IGNORE_TYPE' ) && $this->currentCert['tbsCertList']['issuer'] === $ca['tbsCertificate']['subject']: // @codingStandardsIgnoreLine.
							case defined( 'FILE_X509_IGNORE_TYPE' ) && $this->getDN( self::DN_STRING, $this->currentCert['tbsCertList']['issuer'] ) === $this->getDN( self::DN_STRING, $ca['tbsCertificate']['subject'] ): // @codingStandardsIgnoreLine.
								$authorityKey = $this->getExtension( 'id-ce-authorityKeyIdentifier' ); // @codingStandardsIgnoreLine.
								$subjectKeyID = $this->getExtension( 'id-ce-subjectKeyIdentifier', $ca ); // @codingStandardsIgnoreLine.
								switch ( true ) {
									case ! is_array( $authorityKey ): // @codingStandardsIgnoreLine.
									case is_array( $authorityKey ) && isset( $authorityKey['keyIdentifier'] ) && $authorityKey['keyIdentifier'] === $subjectKeyID: // @codingStandardsIgnoreLine.
										$signingCert = $ca; // @codingStandardsIgnoreLine.
										break 3;
								}
						}
					}
				}
				if ( ! isset( $signingCert ) ) { // @codingStandardsIgnoreLine.
					return false;
				}
				return $this->_validateSignature(
					$signingCert['tbsCertificate']['subjectPublicKeyInfo']['algorithm']['algorithm'], // @codingStandardsIgnoreLine.
					$signingCert['tbsCertificate']['subjectPublicKeyInfo']['subjectPublicKey'], // @codingStandardsIgnoreLine.
					$this->currentCert['signatureAlgorithm']['algorithm'], // @codingStandardsIgnoreLine.
					substr( base64_decode( $this->currentCert['signature'] ), 1), // @codingStandardsIgnoreLine.
					$this->signatureSubject // @codingStandardsIgnoreLine.
				);
			default:
				return false;
		}
	}

	/**
	 * Validates a signature
	 *
	 * Returns true if the signature is verified, false if it is not correct or null on error
	 *
	 * @param string $publicKeyAlgorithm .
	 * @param string $publicKey .
	 * @param string $signatureAlgorithm .
	 * @param string $signature .
	 * @param string $signatureSubject .
	 * @access private
	 * @return int
	 */
	private function _validateSignature( $publicKeyAlgorithm, $publicKey, $signatureAlgorithm, $signature, $signatureSubject ) { // @codingStandardsIgnoreLine.
		switch ( $publicKeyAlgorithm ) { // @codingStandardsIgnoreLine.
			case 'rsaEncryption':
				$rsa = new RSA();
				$rsa->loadKey( $publicKey ); // @codingStandardsIgnoreLine.
				switch ( $signatureAlgorithm ) { // @codingStandardsIgnoreLine.
					case 'md2WithRSAEncryption':
					case 'md5WithRSAEncryption':
					case 'sha1WithRSAEncryption':
					case 'sha224WithRSAEncryption':
					case 'sha256WithRSAEncryption':
					case 'sha384WithRSAEncryption':
					case 'sha512WithRSAEncryption':
						$rsa->setHash( preg_replace( '#WithRSAEncryption$#', '', $signatureAlgorithm ) ); // @codingStandardsIgnoreLine.
						$rsa->setSignatureMode( RSA::SIGNATURE_PKCS1 );
						if ( ! @$rsa->verify( $signatureSubject, $signature ) ) { // @codingStandardsIgnoreLine.
							return false;
						}
						break;
					default:
						return null;
				}
				break;
			default:
				return null;
		}
		return true;
	}

	/**
	 * Reformat public keys
	 *
	 * Reformats a public key to a format supported by phpseclib (if applicable)
	 *
	 * @param string $algorithm .
	 * @param string $key .
	 * @access private
	 * @return string
	 */
	private function _reformatKey( $algorithm, $key ) { // @codingStandardsIgnoreLine.
		switch ( $algorithm ) {
			case 'rsaEncryption':
				return // @codingStandardsIgnoreLine.
					"-----BEGIN RSA PUBLIC KEY-----\r\n" .
					// subjectPublicKey is stored as a bit string in X.509 certs.  the first byte of a bit string represents how many bits
					// in the last byte should be ignored.  the following only supports non-zero stuff but as none of the X.509 certs Firefox
					// uses as a cert authority actually use a non-zero bit I think it's safe to assume that none do.
					chunk_split( base64_encode( substr( base64_decode( $key ), 1 ) ), 64 ) .
					'-----END RSA PUBLIC KEY-----';
			default:
				return $key;
		}
	}

	/**
	 * Decodes an IP address
	 *
	 * Takes in a base64 encoded "blob" and returns a human readable IP address
	 *
	 * @param string $ip .
	 * @access private
	 * @return string
	 */
	private function _decodeIP( $ip ) { // @codingStandardsIgnoreLine.
		return inet_ntop( base64_decode( $ip ) );
	}

	/**
	 * Encodes an IP address
	 *
	 * Takes a human readable IP address into a base64-encoded "blob"
	 *
	 * @param string $ip .
	 * @access private
	 * @return string
	 */
	private function _encodeIP( $ip ) { // @codingStandardsIgnoreLine.
		return base64_encode( inet_pton( $ip ) );
	}

	/**
	 * "Normalizes" a Distinguished Name property
	 *
	 * @param string $propName .
	 * @access private
	 * @return mixed
	 */
	private function _translateDNProp( $propName ) { // @codingStandardsIgnoreLine.
		switch ( strtolower( $propName ) ) { // @codingStandardsIgnoreLine.
			case 'id-at-countryname':
			case 'countryname':
			case 'c':
				return 'id-at-countryName';
			case 'id-at-organizationname':
			case 'organizationname':
			case 'o':
				return 'id-at-organizationName';
			case 'id-at-dnqualifier':
			case 'dnqualifier':
				return 'id-at-dnQualifier';
			case 'id-at-commonname':
			case 'commonname':
			case 'cn':
				return 'id-at-commonName';
			case 'id-at-stateorprovincename':
			case 'stateorprovincename':
			case 'state':
			case 'province':
			case 'provincename':
			case 'st':
				return 'id-at-stateOrProvinceName';
			case 'id-at-localityname':
			case 'localityname':
			case 'l':
				return 'id-at-localityName';
			case 'id-emailaddress':
			case 'emailaddress':
				return 'pkcs-9-at-emailAddress';
			case 'id-at-serialnumber':
			case 'serialnumber':
				return 'id-at-serialNumber';
			case 'id-at-postalcode':
			case 'postalcode':
				return 'id-at-postalCode';
			case 'id-at-streetaddress':
			case 'streetaddress':
				return 'id-at-streetAddress';
			case 'id-at-name':
			case 'name':
				return 'id-at-name';
			case 'id-at-givenname':
			case 'givenname':
				return 'id-at-givenName';
			case 'id-at-surname':
			case 'surname':
			case 'sn':
				return 'id-at-surname';
			case 'id-at-initials':
			case 'initials':
				return 'id-at-initials';
			case 'id-at-generationqualifier':
			case 'generationqualifier':
				return 'id-at-generationQualifier';
			case 'id-at-organizationalunitname':
			case 'organizationalunitname':
			case 'ou':
				return 'id-at-organizationalUnitName';
			case 'id-at-pseudonym':
			case 'pseudonym':
				return 'id-at-pseudonym';
			case 'id-at-title':
			case 'title':
				return 'id-at-title';
			case 'id-at-description':
			case 'description':
				return 'id-at-description';
			case 'id-at-role':
			case 'role':
				return 'id-at-role';
			case 'id-at-uniqueidentifier':
			case 'uniqueidentifier':
			case 'x500uniqueidentifier':
				return 'id-at-uniqueIdentifier';
			case 'postaladdress':
			case 'id-at-postaladdress':
				return 'id-at-postalAddress';
			default:
				return false;
		}
	}

	/**
	 * Set a Distinguished Name property
	 *
	 * @param string $propName .
	 * @param mixed  $propValue .
	 * @param string $type optional .
	 * @access public
	 * @return bool
	 */
	public function setDNProp( $propName, $propValue, $type = 'utf8String' ) { // @codingStandardsIgnoreLine.
		if ( empty( $this->dn ) ) {
				$this->dn = array( 'rdnSequence' => array() );
		}
		if ( ( $propName = $this->_translateDNProp( $propName ) ) === false ) { // @codingStandardsIgnoreLine.
			return false;
		}
		foreach ( (array) $propValue as $v ) { // @codingStandardsIgnoreLine.
			if ( ! is_array( $v ) && isset( $type ) ) {
					$v = array( $type => $v );
			}
			$this->dn['rdnSequence'][] = array(
				array(
					'type'  => $propName, // @codingStandardsIgnoreLine.
					'value' => $v,
				),
			);
		}

			return true;
	}

	/**
	 * Remove Distinguished Name properties
	 *
	 * @param string $propName .
	 * @access public
	 */
	public function removeDNProp( $propName ) { // @codingStandardsIgnoreLine.
		if ( empty( $this->dn ) ) {
			return;
		}
		if ( ( $propName = $this->_translateDNProp( $propName ) ) === false ) { // @codingStandardsIgnoreLine.
			return;
		}
		$dn   = &$this->dn['rdnSequence'];
		$size = count( $dn );
		for ( $i = 0; $i < $size; $i++ ) {
			if ( $dn[ $i ][0]['type'] == $propName ) { // @codingStandardsIgnoreLine.
				unset( $dn[ $i ] );
			}
		}
		$dn = array_values( $dn );
	}

	/**
	 * Get Distinguished Name properties
	 *
	 * @param string $propName .
	 * @param array  $dn optional .
	 * @param bool   $withType optional .
	 * @return mixed
	 * @access public
	 */
	public function getDNProp( $propName, $dn = null, $withType = false ) { // @codingStandardsIgnoreLine.
		if ( ! isset( $dn ) ) {
			$dn = $this->dn;
		}
		if ( empty( $dn ) ) {
			return false;
		}
		if ( ( $propName = $this->_translateDNProp( $propName ) ) === false ) { // @codingStandardsIgnoreLine.
			return false;
		}
		$asn1 = new ASN1();
		$asn1->loadOIDs( $this->oids );
		$filters          = array();
		$filters['value'] = array( 'type' => ASN1::TYPE_UTF8_STRING );
		$asn1->loadFilters( $filters );
		$this->_mapOutDNs( $dn, 'rdnSequence', $asn1 );
		$dn     = $dn['rdnSequence'];
		$result = array();
		for ( $i = 0; $i < count( $dn ); $i++ ) { // @codingStandardsIgnoreLine.
			if ( $dn[ $i ][0]['type'] == $propName ) { // @codingStandardsIgnoreLine.
				$v = $dn[ $i ][0]['value'];
				if ( ! $withType ) { // @codingStandardsIgnoreLine.
					if ( is_array( $v ) ) {
						foreach ( $v as $type => $s ) {
							$type = array_search( $type, $asn1->ANYmap, true ); // @codingStandardsIgnoreLine.
							if ( false !== $type && isset( $asn1->stringTypeSize[ $type ] ) ) { // @codingStandardsIgnoreLine.
								$s = $asn1->convert( $s, $type );
								if ( false !== $s ) {
									$v = $s;
									break;
								}
							}
						}
						if ( is_array( $v ) ) {
							$v = array_pop( $v ); // Always strip data type.
						}
					} elseif ( is_object( $v ) && $v instanceof Element ) {
						$map = $this->_getMapping( $propName ); // @codingStandardsIgnoreLine.
						if ( ! is_bool( $map ) ) {
							$decoded = $asn1->decodeBER( $v );
							$v       = $asn1->asn1map( $decoded[0], $map );
						}
					}
				}
				$result[] = $v;
			}
		}
		return $result;
	}

	/**
	 * Set a Distinguished Name
	 *
	 * @param mixed  $dn .
	 * @param bool   $merge optional .
	 * @param string $type optional .
	 * @access public
	 * @return bool
	 */
	public function setDN( $dn, $merge = false, $type = 'utf8String' ) { // @codingStandardsIgnoreLine.
		if ( ! $merge ) {
			$this->dn = null;
		}
		if ( is_array( $dn ) ) {
			if ( isset( $dn['rdnSequence'] ) ) {
				$this->dn = $dn; // No merge here.
				return true;
			}
			// handles stuff generated by openssl_x509_parse() .
			foreach ( $dn as $prop => $value ) {
				if ( ! $this->setDNProp( $prop, $value, $type ) ) {
					return false;
				}
			}
			return true;
		}
		// handles everything else .
		$results = preg_split( '#((?:^|, *|/)(?:C=|O=|OU=|CN=|L=|ST=|SN=|postalCode=|streetAddress=|emailAddress=|serialNumber=|organizationalUnitName=|title=|description=|role=|x500UniqueIdentifier=|postalAddress=))#', $dn, -1, PREG_SPLIT_DELIM_CAPTURE );
		for ( $i = 1; $i < count( $results ); $i+=2 ) { // @codingStandardsIgnoreLine.
			$prop  = trim( $results[ $i ], ', =/' );
			$value = $results[ $i + 1 ];
			if ( ! $this->setDNProp( $prop, $value, $type ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Get the Distinguished Name for a certificates subject
	 *
	 * @param mixed $format optional .
	 * @param array $dn optional .
	 * @access public
	 * @return bool
	 */
	public function getDN( $format = self::DN_ARRAY, $dn = null ) { // @codingStandardsIgnoreLine.
		if ( ! isset( $dn ) ) {
			$dn = isset( $this->currentCert['tbsCertList'] ) ? $this->currentCert['tbsCertList']['issuer'] : $this->dn; // @codingStandardsIgnoreLine.
		}
		switch ( (int) $format ) {
			case self::DN_ARRAY:
				return $dn;
			case self::DN_ASN1:
				$asn1 = new ASN1();
				$asn1->loadOIDs( $this->oids );
				$filters                         = array();
				$filters['rdnSequence']['value'] = array( 'type' => ASN1::TYPE_UTF8_STRING );
				$asn1->loadFilters( $filters );
				$this->_mapOutDNs( $dn, 'rdnSequence', $asn1 );
				return $asn1->encodeDER( $dn, $this->Name ); // @codingStandardsIgnoreLine.
			case self::DN_CANON:
				// No SEQUENCE around RDNs and all string values normalized as.
				// trimmed lowercase UTF-8 with all spacing as one blank.
				// constructed RDNs will not be canonicalized.
				$asn1 = new ASN1();
				$asn1->loadOIDs( $this->oids );
				$filters          = array();
				$filters['value'] = array( 'type' => ASN1::TYPE_UTF8_STRING );
				$asn1->loadFilters( $filters );
				$result = '';
				$this->_mapOutDNs( $dn, 'rdnSequence', $asn1 );
				foreach ( $dn['rdnSequence'] as $rdn ) {
					foreach ( $rdn as $i => $attr ) {
						$attr = &$rdn[ $i ];
						if ( is_array( $attr['value'] ) ) {
							foreach ( $attr['value'] as $type => $v ) {
								$type = array_search( $type, $asn1->ANYmap, true ); // @codingStandardsIgnoreLine.
								if ( false !== $type && isset( $asn1->stringTypeSize[ $type ] ) ) { // @codingStandardsIgnoreLine.
									$v = $asn1->convert( $v, $type );
									if ( false !== $v ) {
										$v             = preg_replace( '/\s+/', ' ', $v );
										$attr['value'] = strtolower( trim( $v ) );
										break;
									}
								}
							}
						}
					}
					$result .= $asn1->encodeDER( $rdn, $this->RelativeDistinguishedName ); // @codingStandardsIgnoreLine.
				}
				return $result;
			case self::DN_HASH:
				$dn   = $this->getDN( self::DN_CANON, $dn );
				$hash = new Hash( 'sha1' );
				$hash = $hash->hash( $dn );
				extract( unpack( 'Vhash', $hash ) ); // @codingStandardsIgnoreLine.
				return strtolower( bin2hex( pack( 'N', $hash ) ) );
		}
		// Default is to return a string.
		$start  = true;
		$output = '';
		$result = array();
		$asn1   = new ASN1();
		$asn1->loadOIDs( $this->oids );
		$filters                         = array();
		$filters['rdnSequence']['value'] = array( 'type' => ASN1::TYPE_UTF8_STRING );
		$asn1->loadFilters( $filters );
		$this->_mapOutDNs( $dn, 'rdnSequence', $asn1 );
		foreach ( $dn['rdnSequence'] as $field ) {
			$prop  = $field[0]['type'];
			$value = $field[0]['value'];
			$delim = ', ';
			switch ( $prop ) {
				case 'id-at-countryName':
					$desc = 'C';
					break;
				case 'id-at-stateOrProvinceName':
					$desc = 'ST';
					break;
				case 'id-at-organizationName':
					$desc = 'O';
					break;
				case 'id-at-organizationalUnitName':
					$desc = 'OU';
					break;
				case 'id-at-commonName':
					$desc = 'CN';
					break;
				case 'id-at-localityName':
					$desc = 'L';
					break;
				case 'id-at-surname':
					$desc = 'SN';
					break;
				case 'id-at-uniqueIdentifier':
					$delim = '/';
					$desc  = 'x500UniqueIdentifier';
					break;
				case 'id-at-postalAddress':
					$delim = '/';
					$desc  = 'postalAddress';
					break;
				default:
					$delim = '/';
					$desc  = preg_replace( '#.+-([^-]+)$#', '$1', $prop );
			}
			if ( ! $start ) {
				$output .= $delim;
			}
			if ( is_array( $value ) ) {
				foreach ( $value as $type => $v ) {
					$type = array_search( $type, $asn1->ANYmap, true ); // @codingStandardsIgnoreLine.
					if ( $type !== false && isset( $asn1->stringTypeSize[ $type ] ) ) { // @codingStandardsIgnoreLine.
						$v = $asn1->convert( $v, $type );
						if ( false !== $v ) {
							$value = $v;
							break;
						}
					}
				}
				if ( is_array( $value ) ) {
					$value = array_pop( $value ); // Always strip data type.
				}
			} elseif ( is_object( $value ) && $value instanceof Element ) {
				$callback = create_function( '$x', 'return "\x" . bin2hex($x[0]);' ); // @codingStandardsIgnoreLine.
				$value    = strtoupper( preg_replace_callback( '#[^\x20-\x7E]#', $callback, $value->element ) );
			}
			$output         .= $desc . '=' . $value;
			$result[ $desc ] = isset( $result[ $desc ] ) ?
				array_merge( (array) $dn[ $prop ], array( $value ) ) :
				$value;
			$start           = false;
		}
		return self::DN_OPENSSL == $format ? $result : $output; // WPCS:Loose comparison ok.
	}

	/**
	 * Get the Distinguished Name for a certificate/crl issuer
	 *
	 * @param int $format optional .
	 * @access public
	 * @return mixed
	 */
	public function getIssuerDN( $format = self::DN_ARRAY ) { // @codingStandardsIgnoreLine.
		switch ( true ) {
			case ! isset( $this->currentCert ) || ! is_array( $this->currentCert ): // @codingStandardsIgnoreLine.
				break;
			case isset( $this->currentCert['tbsCertificate'] ): // @codingStandardsIgnoreLine.
				return $this->getDN( $format, $this->currentCert['tbsCertificate']['issuer'] ); // @codingStandardsIgnoreLine.
			case isset( $this->currentCert['tbsCertList'] ): // @codingStandardsIgnoreLine.
				return $this->getDN( $format, $this->currentCert['tbsCertList']['issuer'] ); // @codingStandardsIgnoreLine.
		}
		return false;
	}

	/**
	 * Get the Distinguished Name for a certificate/csr subject
	 * Alias of getDN()
	 *
	 * @param int $format optional .
	 * @access public
	 * @return mixed
	 */
	public function getSubjectDN( $format = self::DN_ARRAY ) { // @codingStandardsIgnoreLine.
		switch ( true ) {
			case ! empty( $this->dn ):
				return $this->getDN( $format );
			case ! isset( $this->currentCert ) || ! is_array( $this->currentCert ): // @codingStandardsIgnoreLine.
				break;
			case isset( $this->currentCert['tbsCertificate'] ): // @codingStandardsIgnoreLine.
				return $this->getDN( $format, $this->currentCert['tbsCertificate']['subject'] ); // @codingStandardsIgnoreLine.
			case isset( $this->currentCert['certificationRequestInfo'] ): // @codingStandardsIgnoreLine.
				return $this->getDN( $format, $this->currentCert['certificationRequestInfo']['subject'] ); // @codingStandardsIgnoreLine.
		}
		return false;
	}

	/**
	 * Get an individual Distinguished Name property for a certificate/crl issuer
	 *
	 * @param string $propName .
	 * @param bool   $withType optional .
	 * @access public
	 * @return mixed
	 */
	public function getIssuerDNProp( $propName, $withType = false ) { // @codingStandardsIgnoreLine.
		switch ( true ) {
			case ! isset( $this->currentCert ) || ! is_array( $this->currentCert ): // @codingStandardsIgnoreLine.
				break;
			case isset( $this->currentCert['tbsCertificate'] ): // @codingStandardsIgnoreLine.
				return $this->getDNProp( $propName, $this->currentCert['tbsCertificate']['issuer'], $withType ); // @codingStandardsIgnoreLine.
			case isset( $this->currentCert['tbsCertList'] ): // @codingStandardsIgnoreLine.
				return $this->getDNProp( $propName, $this->currentCert['tbsCertList']['issuer'], $withType ); // @codingStandardsIgnoreLine.
		}
		return false;
	}

	/**
	 * Get an individual Distinguished Name property for a certificate/csr subject
	 *
	 * @param string $propName .
	 * @param bool   $withType optional .
	 * @access public
	 * @return mixed
	 */
	public function getSubjectDNProp( $propName, $withType = false ) { // @codingStandardsIgnoreLine.
		switch ( true ) {
			case ! empty( $this->dn ):
				return $this->getDNProp( $propName, null, $withType ); // @codingStandardsIgnoreLine.
			case ! isset( $this->currentCert ) || ! is_array( $this->currentCert ): // @codingStandardsIgnoreLine.
				break;
			case isset( $this->currentCert['tbsCertificate'] ): // @codingStandardsIgnoreLine.
				return $this->getDNProp( $propName, $this->currentCert['tbsCertificate']['subject'], $withType ); // @codingStandardsIgnoreLine.
			case isset( $this->currentCert['certificationRequestInfo'] ): // @codingStandardsIgnoreLine.
				return $this->getDNProp( $propName, $this->currentCert['certificationRequestInfo']['subject'], $withType ); // @codingStandardsIgnoreLine.
		}
		return false;
	}

	/**
	 * Get the certificate chain for the current cert
	 *
	 * @access public
	 * @return mixed
	 */
	public function getChain() { // @codingStandardsIgnoreLine.
		$chain = array( $this->currentCert ); // @codingStandardsIgnoreLine.
		if ( ! is_array( $this->currentCert ) || ! isset( $this->currentCert['tbsCertificate'] ) ) { // @codingStandardsIgnoreLine.
			return false;
		}
		if ( empty( $this->CAs ) ) { // @codingStandardsIgnoreLine.
			return $chain;
		}
		while ( true ) {
			$currentCert = $chain[ count( $chain ) - 1 ]; // @codingStandardsIgnoreLine.
			for ( $i = 0; $i < count( $this->CAs ); $i++ ) { // @codingStandardsIgnoreLine.
				$ca = $this->CAs[ $i ]; // @codingStandardsIgnoreLine.
				if ( $currentCert['tbsCertificate']['issuer'] === $ca['tbsCertificate']['subject'] ) { // @codingStandardsIgnoreLine.
					$authorityKey = $this->getExtension( 'id-ce-authorityKeyIdentifier', $currentCert ); // @codingStandardsIgnoreLine.
					$subjectKeyID = $this->getExtension( 'id-ce-subjectKeyIdentifier', $ca ); // @codingStandardsIgnoreLine.
					switch ( true ) {
						case ! is_array( $authorityKey ): // @codingStandardsIgnoreLine.
						case is_array( $authorityKey ) && isset( $authorityKey['keyIdentifier'] ) && $authorityKey['keyIdentifier'] === $subjectKeyID: // @codingStandardsIgnoreLine.
							if ( $currentCert === $ca ) { // @codingStandardsIgnoreLine.
								break 3;
							}
							$chain[] = $ca;
							break 2;
					}
				}
			}
			if ( $i == count( $this->CAs ) ) { // @codingStandardsIgnoreLine.
				break;
			}
		}
		foreach ( $chain as $key => $value ) {
			$chain[ $key ] = new X509();
			$chain[ $key ]->loadX509( $value );
		}
		return $chain;
	}

	/**
	 * Set public key
	 *
	 * Key needs to be a \phpseclib\Crypt\RSA object
	 *
	 * @param object $key .
	 * @access public
	 */
	public function setPublicKey( $key ) { // @codingStandardsIgnoreLine.
		$key->setPublicKey();
		$this->publicKey = $key; // @codingStandardsIgnoreLine.
	}

	/**
	 * Set private key
	 *
	 * Key needs to be a \phpseclib\Crypt\RSA object
	 *
	 * @param object $key .
	 * @access public
	 */
	public function setPrivateKey( $key ) { // @codingStandardsIgnoreLine.
			$this->privateKey = $key; // @codingStandardsIgnoreLine.
	}

	/**
	 * Set challenge
	 *
	 * Used for SPKAC CSR's
	 *
	 * @param string $challenge .
	 * @access public
	 */
	function setChallenge( $challenge ) { // @codingStandardsIgnoreLine.
		$this->challenge = $challenge;
	}

	/**
	 * Gets the public key
	 *
	 * Returns a \phpseclib\Crypt\RSA object or a false.
	 *
	 * @access public
	 * @return mixed
	 */
	public function getPublicKey() { // @codingStandardsIgnoreLine.
		if ( isset( $this->publicKey ) ) { // @codingStandardsIgnoreLine.
			return $this->publicKey; // @codingStandardsIgnoreLine.
		}
		if ( isset( $this->currentCert ) && is_array( $this->currentCert ) ) { // @codingStandardsIgnoreLine.
			foreach ( array( 'tbsCertificate/subjectPublicKeyInfo', 'certificationRequestInfo/subjectPKInfo' ) as $path ) {
				$keyinfo = $this->_subArray( $this->currentCert, $path ); // @codingStandardsIgnoreLine.
				if ( ! empty( $keyinfo ) ) {
					break;
				}
			}
		}
		if ( empty( $keyinfo ) ) {
			return false;
		}
		$key = $keyinfo['subjectPublicKey'];
		switch ( $keyinfo['algorithm']['algorithm'] ) {
			case 'rsaEncryption':
				$publicKey = new RSA(); // @codingStandardsIgnoreLine.
				$publicKey->loadKey( $key ); // @codingStandardsIgnoreLine.
				$publicKey->setPublicKey(); // @codingStandardsIgnoreLine.
				break;
			default:
				return false;
		}
		return $publicKey; // @codingStandardsIgnoreLine.
	}

	/**
	 * Load a Certificate Signing Request
	 *
	 * @param string $csr .
	 * @param string $mode .
	 * @access public
	 * @return mixed
	 */
	public function loadCSR( $csr, $mode = self::FORMAT_AUTO_DETECT ) { // @codingStandardsIgnoreLine.
		if ( is_array( $csr ) && isset( $csr['certificationRequestInfo'] ) ) {
			unset( $this->currentCert ); // @codingStandardsIgnoreLine.
			unset( $this->currentKeyIdentifier ); // @codingStandardsIgnoreLine.
			unset( $this->signatureSubject ); // @codingStandardsIgnoreLine.
			$this->dn = $csr['certificationRequestInfo']['subject'];
			if ( ! isset( $this->dn ) ) {
				return false;
			}
			$this->currentCert = $csr; // @codingStandardsIgnoreLine.
			return $csr;
		}
		$asn1 = new ASN1();
		if ( self::FORMAT_DER != $mode ) { // WPCS:Loose comparison ok.
			$newcsr = $this->_extractBER( $csr );
			if ( self::FORMAT_PEM == $mode && $csr == $newcsr ) { // WPCS:Loose comparison ok.
				return false;
			}
			$csr = $newcsr;
		}
		$orig = $csr;
		if ( false === $csr ) {
			$this->currentCert = false; // @codingStandardsIgnoreLine.
			return false;
		}
		$asn1->loadOIDs( $this->oids );
		$decoded = $asn1->decodeBER( $csr );
		if ( empty( $decoded ) ) {
			$this->currentCert = false; // @codingStandardsIgnoreLine.
			return false;
		}
		$csr = $asn1->asn1map( $decoded[0], $this->CertificationRequest ); // @codingStandardsIgnoreLine.
		if ( ! isset( $csr ) || false === $csr ) {
			$this->currentCert = false; // @codingStandardsIgnoreLine.
			return false;
		}
		$this->_mapInAttributes( $csr, 'certificationRequestInfo/attributes', $asn1 );
		$this->_mapInDNs( $csr, 'certificationRequestInfo/subject/rdnSequence', $asn1 );
		$this->dn               = $csr['certificationRequestInfo']['subject'];
		$this->signatureSubject = substr( $orig, $decoded[0]['content'][0]['start'], $decoded[0]['content'][0]['length'] ); // @codingStandardsIgnoreLine.
		$algorithm              = &$csr['certificationRequestInfo']['subjectPKInfo']['algorithm']['algorithm'];
		$key                    = &$csr['certificationRequestInfo']['subjectPKInfo']['subjectPublicKey'];
		$key                    = $this->_reformatKey( $algorithm, $key );
		switch ( $algorithm ) {
			case 'rsaEncryption':
				$this->publicKey = new RSA(); // @codingStandardsIgnoreLine.
				$this->publicKey->loadKey( $key ); // @codingStandardsIgnoreLine.
				$this->publicKey->setPublicKey(); // @codingStandardsIgnoreLine.
				break;
			default:
				$this->publicKey = null; // @codingStandardsIgnoreLine.
		}
		$this->currentKeyIdentifier = null; // @codingStandardsIgnoreLine.
		$this->currentCert          = $csr; // @codingStandardsIgnoreLine.
		return $csr;
	}

	/**
	 * Save CSR request
	 *
	 * @param array $csr .
	 * @param int   $format optional .
	 * @access public
	 * @return string
	 */
	public function saveCSR( $csr, $format = self::FORMAT_PEM ) { // @codingStandardsIgnoreLine.
		if ( ! is_array( $csr ) || ! isset( $csr['certificationRequestInfo'] ) ) {
			return false;
		}
		switch ( true ) {
			case ! ( $algorithm = $this->_subArray( $csr, 'certificationRequestInfo/subjectPKInfo/algorithm/algorithm' ) ): // @codingStandardsIgnoreLine.
			case is_object( $csr['certificationRequestInfo']['subjectPKInfo']['subjectPublicKey'] ):
				break;
			default:
				switch ( $algorithm ) {
					case 'rsaEncryption':
						$csr['certificationRequestInfo']['subjectPKInfo']['subjectPublicKey']
									= base64_encode( "\0" . base64_decode( preg_replace( '#-.+-|[\r\n]#', '', $csr['certificationRequestInfo']['subjectPKInfo']['subjectPublicKey'] ) ) );

						$csr['certificationRequestInfo']['subjectPKInfo']['algorithm']['parameters'] = null;
						$csr['signatureAlgorithm']['parameters']                                     = null;
						$csr['certificationRequestInfo']['signature']['parameters']                  = null;
				}
		}
		$asn1 = new ASN1();
		$asn1->loadOIDs( $this->oids );
		$filters = array();
		$filters['certificationRequestInfo']['subject']['rdnSequence']['value']
				= array( 'type' => ASN1::TYPE_UTF8_STRING );
		$asn1->loadFilters( $filters );
		$this->_mapOutDNs( $csr, 'certificationRequestInfo/subject/rdnSequence', $asn1 );
		$this->_mapOutAttributes( $csr, 'certificationRequestInfo/attributes', $asn1 );
		$csr = $asn1->encodeDER( $csr, $this->CertificationRequest ); // @codingStandardsIgnoreLine.
		switch ( $format ) {
			case self::FORMAT_DER:
				return $csr;
			default:
				return "-----BEGIN CERTIFICATE REQUEST-----\r\n" . chunk_split( base64_encode( $csr ), 64 ) . '-----END CERTIFICATE REQUEST-----';
		}
	}

	/**
	 * Load a SPKAC CSR
	 *
	 * SPKAC's are produced by the HTML5 keygen element:
	 *
	 * https://developer.mozilla.org/en-US/docs/HTML/Element/keygen
	 *
	 * @param string $spkac .
	 * @access public
	 * @return mixed
	 */
	public function loadSPKAC( $spkac ) { // @codingStandardsIgnoreLine.
		if ( is_array( $spkac ) && isset( $spkac['publicKeyAndChallenge'] ) ) {
			unset( $this->currentCert ); // @codingStandardsIgnoreLine.
			unset( $this->currentKeyIdentifier ); // @codingStandardsIgnoreLine.
			unset( $this->signatureSubject ); // @codingStandardsIgnoreLine.
			$this->currentCert = $spkac; // @codingStandardsIgnoreLine.
			return $spkac;
		}
		$asn1 = new ASN1();
		$temp = preg_replace( '#(?:SPKAC=)|[ \r\n\\\]#', '', $spkac );
		$temp = preg_match( '#^[a-zA-Z\d/+]*={0,2}$#', $temp ) ? base64_decode( $temp ) : false;
		if ( false != $temp ) { // WPCS:Loose comparison ok.
			$spkac = $temp;
		}
		$orig = $spkac;
		if ( false === $spkac ) {
			$this->currentCert = false; // @codingStandardsIgnoreLine.
			return false;
		}
		$asn1->loadOIDs( $this->oids );
		$decoded = $asn1->decodeBER( $spkac );
		if ( empty( $decoded ) ) {
			$this->currentCert = false; // @codingStandardsIgnoreLine.
			return false;
		}
		$spkac = $asn1->asn1map( $decoded[0], $this->SignedPublicKeyAndChallenge ); // @codingStandardsIgnoreLine.
		if ( ! isset( $spkac ) || false === $spkac ) {
			$this->currentCert = false; // @codingStandardsIgnoreLine.
			return false;
		}

		$this->signatureSubject = substr( $orig, $decoded[0]['content'][0]['start'], $decoded[0]['content'][0]['length'] ); // @codingStandardsIgnoreLine.
		$algorithm              = &$spkac['publicKeyAndChallenge']['spki']['algorithm']['algorithm'];
		$key                    = &$spkac['publicKeyAndChallenge']['spki']['subjectPublicKey'];
		$key                    = $this->_reformatKey( $algorithm, $key );

		switch ( $algorithm ) {
			case 'rsaEncryption':
				$this->publicKey = new RSA(); // @codingStandardsIgnoreLine.
				$this->publicKey->loadKey( $key ); // @codingStandardsIgnoreLine.
				$this->publicKey->setPublicKey(); // @codingStandardsIgnoreLine.
				break;
			default:
				$this->publicKey = null; // @codingStandardsIgnoreLine.
		}
		$this->currentKeyIdentifier = null; // @codingStandardsIgnoreLine.
		$this->currentCert          = $spkac; // @codingStandardsIgnoreLine.
		return $spkac;
	}

	/**
	 * Save a SPKAC CSR request
	 *
	 * @param array $spkac .
	 * @param int   $format optional .
	 * @access public
	 * @return string
	 */
	public function saveSPKAC( $spkac, $format = self::FORMAT_PEM ) { // @codingStandardsIgnoreLine.
		if ( ! is_array( $spkac ) || ! isset( $spkac['publicKeyAndChallenge'] ) ) {
			return false;
		}

		$algorithm = $this->_subArray( $spkac, 'publicKeyAndChallenge/spki/algorithm/algorithm' );
		switch ( true ) {
			case ! $algorithm:
			case is_object( $spkac['publicKeyAndChallenge']['spki']['subjectPublicKey'] ):
				break;
			default:
				switch ( $algorithm ) {
					case 'rsaEncryption':
						$spkac['publicKeyAndChallenge']['spki']['subjectPublicKey']
							= base64_encode( "\0" . base64_decode( preg_replace( '#-.+-|[\r\n]#', '', $spkac['publicKeyAndChallenge']['spki']['subjectPublicKey'] ) ) );
				}
		}

		$asn1 = new ASN1();
		$asn1->loadOIDs( $this->oids );
		$spkac = $asn1->encodeDER( $spkac, $this->SignedPublicKeyAndChallenge ); // @codingStandardsIgnoreLine.
		switch ( $format ) {
			case self::FORMAT_DER:
				return $spkac;
			default:
				// OpenSSL's implementation of SPKAC requires the SPKAC be preceded by SPKAC= and since there are pretty much .
				// no other SPKAC decoders phpseclib will use that same format .
				return 'SPKAC=' . base64_encode( $spkac );
		}
	}

	/**
	 * Load a Certificate Revocation List
	 *
	 * @param string $crl .
	 * @param string $mode .
	 * @access public
	 * @return mixed
	 */
	public function loadCRL($crl, $mode = self::FORMAT_AUTO_DETECT) { // @codingStandardsIgnoreLine.
		if ( is_array( $crl ) && isset( $crl['tbsCertList'] ) ) {
			$this->currentCert = $crl; // @codingStandardsIgnoreLine.
			unset($this->signatureSubject); // @codingStandardsIgnoreLine.
			return $crl;
		}

		$asn1 = new ASN1();
		if ( self::FORMAT_DER != $mode ) {// WPCS: Loose comparison ok.
			$newcrl = $this->_extractBER( $crl );
			if ( self::FORMAT_PEM == $mode && $crl == $newcrl ) { // WPCS:Loose comparison ok.
				return false;
			}
			$crl = $newcrl;
		}
		$orig = $crl;
		if ( false === $crl ) {
			$this->currentCert = false;// @codingStandardsIgnoreLine.
			return false;
		}
		$asn1->loadOIDs( $this->oids );
		$decoded = $asn1->decodeBER( $crl );
		if ( empty( $decoded ) ) {
			$this->currentCert = false;// @codingStandardsIgnoreLine.
			return false;
		}
		$crl = $asn1->asn1map( $decoded[0], $this->CertificateList );// @codingStandardsIgnoreLine.
		if ( ! isset( $crl ) || false === $crl ) {
			$this->currentCert = false;// @codingStandardsIgnoreLine.
			return false;
		}
		$this->signatureSubject = substr( $orig, $decoded[0]['content'][0]['start'], $decoded[0]['content'][0]['length'] );// @codingStandardsIgnoreLine.
		$this->_mapInDNs( $crl, 'tbsCertList/issuer/rdnSequence', $asn1 );
		if ( $this->_isSubArrayValid( $crl, 'tbsCertList/crlExtensions' ) ) {
			$this->_mapInExtensions( $crl, 'tbsCertList/crlExtensions', $asn1 );
		}
		if ( $this->_isSubArrayValid( $crl, 'tbsCertList/revokedCertificates' ) ) {
			$rclist_ref = &$this->_subArrayUnchecked( $crl, 'tbsCertList/revokedCertificates' );
			if ( $rclist_ref ) {
				$rclist = $crl['tbsCertList']['revokedCertificates'];
				foreach ( $rclist as $i => $extension ) {
					if ( $this->_isSubArrayValid( $rclist, "$i/crlEntryExtensions", $asn1 ) ) {
						$this->_mapInExtensions( $rclist_ref, "$i/crlEntryExtensions", $asn1 );
					}
				}
			}
		}
		$this->currentKeyIdentifier = null; // @codingStandardsIgnoreLine.
		$this->currentCert          = $crl; // @codingStandardsIgnoreLine.
		return $crl;
	}

	/**
	 * Save Certificate Revocation List.
	 *
	 * @param array $crl .
	 * @param int   $format optional .
	 * @access public
	 * @return string
	 */
	public function saveCRL( $crl, $format = self::FORMAT_PEM ) {// @codingStandardsIgnoreLine.
		if ( ! is_array( $crl ) || ! isset( $crl['tbsCertList'] ) ) {
			return false;
		}
		$asn1 = new ASN1();
		$asn1->loadOIDs( $this->oids );

		$filters = array();
		$filters['tbsCertList']['issuer']['rdnSequence']['value']
						= array( 'type' => ASN1::TYPE_UTF8_STRING );

		$filters['tbsCertList']['signature']['parameters']
						= array( 'type' => ASN1::TYPE_UTF8_STRING );

		$filters['signatureAlgorithm']['parameters']
						= array( 'type' => ASN1::TYPE_UTF8_STRING );

		if ( empty( $crl['tbsCertList']['signature']['parameters'] ) ) {
				$filters['tbsCertList']['signature']['parameters']
						= array( 'type' => ASN1::TYPE_NULL );
		}
		if ( empty( $crl['signatureAlgorithm']['parameters'] ) ) {
				$filters['signatureAlgorithm']['parameters']
						= array( 'type' => ASN1::TYPE_NULL );
		}
		$asn1->loadFilters( $filters );
		$this->_mapOutDNs( $crl, 'tbsCertList/issuer/rdnSequence', $asn1 );
		$this->_mapOutExtensions( $crl, 'tbsCertList/crlExtensions', $asn1 );
		$rclist = &$this->_subArray( $crl, 'tbsCertList/revokedCertificates' );
		if ( is_array( $rclist ) ) {
			foreach ( $rclist as $i => $extension ) {
				$this->_mapOutExtensions( $rclist, "$i/crlEntryExtensions", $asn1 );
			}
		}
		$crl = $asn1->encodeDER( $crl, $this->CertificateList );// @codingStandardsIgnoreLine.
		switch ( $format ) {
			case self::FORMAT_DER:
				return $crl;
			default:
				return "-----BEGIN X509 CRL-----\r\n" . chunk_split( base64_encode( $crl ), 64 ) . '-----END X509 CRL-----';
		}
	}

	/**
	 * Helper function to build a time field according to RFC 3280 section
	 *  - 4.1.2.5 Validity
	 *  - 5.1.2.4 This Update
	 *  - 5.1.2.5 Next Update
	 *  - 5.1.2.6 Revoked Certificates
	 * by choosing utcTime iff year of date given is before 2050 and generalTime else.
	 *
	 * @param string $date in format date('D, d M Y H:i:s O') .
	 * @access private
	 * @return array
	 */
	private function _timeField( $date ) { // @codingStandardsIgnoreLine.
		$year = @gmdate( "Y", @strtotime( $date ) ); // @codingStandardsIgnoreLine.
		if ( $year < 2050 ) {
				return array( 'utcTime' => $date );
		} else {
				return array( 'generalTime' => $date );
		}
	}

	/**
	 * Sign an X.509 certificate
	 *
	 * $issuer's private key needs to be loaded.
	 * $subject can be either an existing X.509 cert (if you want to resign it),
	 * a CSR or something with the DN and public key explicitly set.
	 *
	 * @param \phpseclib\File\X509 $issuer .
	 * @param \phpseclib\File\X509 $subject .
	 * @param string               $signatureAlgorithm optional .
	 * @access public
	 * @return mixed
	 */
	public function sign( $issuer, $subject, $signatureAlgorithm = 'sha1WithRSAEncryption' ) { // @codingStandardsIgnoreLine.
		if ( ! is_object( $issuer->privateKey ) || empty( $issuer->dn ) ) { // @codingStandardsIgnoreLine.
			return false;
		}
		if ( isset( $subject->publicKey ) && ! ( $subjectPublicKey = $subject->_formatSubjectPublicKey() ) ) { // @codingStandardsIgnoreLine.
			return false;
		}
		$currentCert      = isset( $this->currentCert ) ? $this->currentCert : null; // @codingStandardsIgnoreLine.
		$signatureSubject = isset( $this->signatureSubject ) ? $this->signatureSubject : null; // @codingStandardsIgnoreLine.
		if ( isset( $subject->currentCert ) && is_array( $subject->currentCert ) && isset( $subject->currentCert['tbsCertificate'] ) ) { // @codingStandardsIgnoreLine.
			$this->currentCert = $subject->currentCert; // @codingStandardsIgnoreLine.
			$this->currentCert['tbsCertificate']['signature']['algorithm'] = $signatureAlgorithm; // @codingStandardsIgnoreLine.
			$this->currentCert['signatureAlgorithm']['algorithm']          = $signatureAlgorithm; // @codingStandardsIgnoreLine.
			if ( ! empty( $this->startDate ) ) { // @codingStandardsIgnoreLine.
				$this->currentCert['tbsCertificate']['validity']['notBefore'] = $this->_timeField( $this->startDate ); // @codingStandardsIgnoreLine.
			}
			if ( ! empty( $this->endDate ) ) { // @codingStandardsIgnoreLine.
				$this->currentCert['tbsCertificate']['validity']['notAfter'] = $this->_timeField( $this->endDate ); // @codingStandardsIgnoreLine.
			}
			if ( ! empty( $this->serialNumber ) ) { // @codingStandardsIgnoreLine.
				$this->currentCert['tbsCertificate']['serialNumber'] = $this->serialNumber; // @codingStandardsIgnoreLine.
			}
			if ( ! empty( $subject->dn ) ) {
				$this->currentCert['tbsCertificate']['subject'] = $subject->dn; // @codingStandardsIgnoreLine.
			}
			if ( ! empty( $subject->publicKey ) ) { // @codingStandardsIgnoreLine.
				$this->currentCert['tbsCertificate']['subjectPublicKeyInfo'] = $subjectPublicKey; // @codingStandardsIgnoreLine.
			}
			$this->removeExtension( 'id-ce-authorityKeyIdentifier' );
			if ( isset( $subject->domains ) ) {
				$this->removeExtension( 'id-ce-subjectAltName' );
			}
		} elseif ( isset( $subject->currentCert ) && is_array( $subject->currentCert ) && isset( $subject->currentCert['tbsCertList'] ) ) { // @codingStandardsIgnoreLine.
			return false;
		} else {
			if ( ! isset( $subject->publicKey ) ) { // @codingStandardsIgnoreLine.
				return false;
			}
			$startDate         = ! empty( $this->startDate ) ? $this->startDate : @date( 'D, d M Y H:i:s O' ); // @codingStandardsIgnoreLine.
			$endDate           = ! empty( $this->endDate ) ? $this->endDate : @date( 'D, d M Y H:i:s O', strtotime( '+1 year' ) ); // @codingStandardsIgnoreLine.
			$serialNumber      = ! empty( $this->serialNumber ) ? // @codingStandardsIgnoreLine.
			$this->serialNumber : // @codingStandardsIgnoreLine.
			new BigInteger( Random::string( 20 ) & ( "\x7F" . str_repeat( "\xFF", 19 ) ), 256 );
			$this->currentCert = array( // @codingStandardsIgnoreLine.
				'tbsCertificate'     =>
					array(
						'version'              => 'v3',
						'serialNumber'         => $serialNumber, // @codingStandardsIgnoreLine.
						'signature'            => array( 'algorithm' => $signatureAlgorithm ), // @codingStandardsIgnoreLine.
						'issuer'               => false, // this is going to be overwritten later .
						'validity'             => array(
							'notBefore' => $this->_timeField( $startDate ), // @codingStandardsIgnoreLine.
							'notAfter'  => $this->_timeField( $endDate ), // @codingStandardsIgnoreLine.
						),
						'subject'              => $subject->dn,
						'subjectPublicKeyInfo' => $subjectPublicKey, // @codingStandardsIgnoreLine.
					),
				'signatureAlgorithm' => array( 'algorithm' => $signatureAlgorithm ), // @codingStandardsIgnoreLine.
				'signature'          => false,
			);
			$csrexts           = $subject->getAttribute( 'pkcs-9-at-extensionRequest', 0 );
			if ( ! empty( $csrexts ) ) {
				$this->currentCert['tbsCertificate']['extensions'] = $csrexts; // @codingStandardsIgnoreLine.
			}
		}
		$this->currentCert['tbsCertificate']['issuer'] = $issuer->dn; // @codingStandardsIgnoreLine.
		if ( isset( $issuer->currentKeyIdentifier ) ) { // @codingStandardsIgnoreLine.
			$this->setExtension('id-ce-authorityKeyIdentifier', array(
				'keyIdentifier' => $issuer->currentKeyIdentifier, // @codingStandardsIgnoreLine.
			));
		}
		if ( isset( $subject->currentKeyIdentifier ) ) { // @codingStandardsIgnoreLine.
				$this->setExtension( 'id-ce-subjectKeyIdentifier', $subject->currentKeyIdentifier ); // @codingStandardsIgnoreLine.
		}
		$altName = array(); // @codingStandardsIgnoreLine.
		if ( isset( $subject->domains ) && count( $subject->domains ) ) {
			$altName = array_map( array( '\phpseclib\File\X509', '_dnsName' ), $subject->domains ); // @codingStandardsIgnoreLine.
		}
		if ( isset( $subject->ipAddresses ) && count( $subject->ipAddresses ) ) { // @codingStandardsIgnoreLine.
			$ipAddresses = array(); // @codingStandardsIgnoreLine.
			foreach ( $subject->ipAddresses as $ipAddress ) { // @codingStandardsIgnoreLine.
				$encoded = $subject->_ipAddress( $ipAddress ); // @codingStandardsIgnoreLine.
				if ( false !== $encoded ) {
					$ipAddresses[] = $encoded; // @codingStandardsIgnoreLine.
				}
			}
			if ( count( $ipAddresses ) ) { // @codingStandardsIgnoreLine.
				$altName = array_merge( $altName, $ipAddresses ); // @codingStandardsIgnoreLine.
			}
		}
		if ( ! empty( $altName ) ) { // @codingStandardsIgnoreLine.
			$this->setExtension( 'id-ce-subjectAltName', $altName ); // @codingStandardsIgnoreLine.
		}
		if ( $this->caFlag ) { // @codingStandardsIgnoreLine.
			$keyUsage = $this->getExtension( 'id-ce-keyUsage' ); // @codingStandardsIgnoreLine.
			if ( ! $keyUsage ) { // @codingStandardsIgnoreLine.
				$keyUsage = array(); // @codingStandardsIgnoreLine.
			}
			$this->setExtension(
				'id-ce-keyUsage',
				array_values( array_unique( array_merge( $keyUsage, array( 'cRLSign', 'keyCertSign' ) ) ) ) // @codingStandardsIgnoreLine.
			);
			$basicConstraints = $this->getExtension( 'id-ce-basicConstraints' ); // @codingStandardsIgnoreLine.
			if ( ! $basicConstraints ) { // @codingStandardsIgnoreLine.
				$basicConstraints = array(); // @codingStandardsIgnoreLine.
			}
			$this->setExtension(
				'id-ce-basicConstraints',
				array_unique( array_merge( array( 'cA' => true ), $basicConstraints ) ), // @codingStandardsIgnoreLine.
				true
			);
			if ( ! isset( $subject->currentKeyIdentifier ) ) { // @codingStandardsIgnoreLine.
				$this->setExtension( 'id-ce-subjectKeyIdentifier', base64_encode( $this->computeKeyIdentifier( $this->currentCert ) ), false, false ); // @codingStandardsIgnoreLine.
			}
		}
		// resync $this->signatureSubject
		// save $tbsCertificate in case there are any \phpseclib\File\ASN1\Element objects in it.
		$tbsCertificate = $this->currentCert['tbsCertificate']; // @codingStandardsIgnoreLine.
		$this->loadX509( $this->saveX509( $this->currentCert ) ); // @codingStandardsIgnoreLine.

		$result                   = $this->_sign( $issuer->privateKey, $signatureAlgorithm ); // @codingStandardsIgnoreLine.
		$result['tbsCertificate'] = $tbsCertificate; // @codingStandardsIgnoreLine.

		$this->currentCert      = $currentCert; // @codingStandardsIgnoreLine.
		$this->signatureSubject = $signatureSubject; // @codingStandardsIgnoreLine.
		return $result;
	}

	/**
	 * Sign a CSR
	 *
	 * @param string $signatureAlgorithm .
	 * @access public
	 * @return mixed
	 */
	function signCSR( $signatureAlgorithm = 'sha1WithRSAEncryption' ) { // @codingStandardsIgnoreLine.
		if ( ! is_object( $this->privateKey ) || empty( $this->dn ) ) { // @codingStandardsIgnoreLine.
			return false;
		}
		$origPublicKey   = $this->publicKey; // @codingStandardsIgnoreLine.
		$class           = get_class( $this->privateKey ); // @codingStandardsIgnoreLine.
		$this->publicKey = new $class(); // @codingStandardsIgnoreLine.
		$this->publicKey->loadKey( $this->privateKey->getPublicKey() ); // @codingStandardsIgnoreLine.
		$this->publicKey->setPublicKey(); // @codingStandardsIgnoreLine.
		if ( ! ( $publicKey = $this->_formatSubjectPublicKey() ) ) { // @codingStandardsIgnoreLine.
			return false;
		}
		$this->publicKey  = $origPublicKey; // @codingStandardsIgnoreLine.
		$currentCert      = isset( $this->currentCert ) ? $this->currentCert : null; // @codingStandardsIgnoreLine.
		$signatureSubject = isset( $this->signatureSubject ) ? $this->signatureSubject : null; // @codingStandardsIgnoreLine.
		if ( isset( $this->currentCert ) && is_array( $this->currentCert ) && isset( $this->currentCert['certificationRequestInfo'] ) ) { // @codingStandardsIgnoreLine.
			$this->currentCert['signatureAlgorithm']['algorithm'] = $signatureAlgorithm; // @codingStandardsIgnoreLine.
			if ( ! empty( $this->dn ) ) {
					$this->currentCert['certificationRequestInfo']['subject'] = $this->dn; // @codingStandardsIgnoreLine.
			}
			$this->currentCert['certificationRequestInfo']['subjectPKInfo'] = $publicKey; // @codingStandardsIgnoreLine.
		} else {
			$this->currentCert = array( // @codingStandardsIgnoreLine.
				'certificationRequestInfo' =>
					array(
						'version'       => 'v1',
						'subject'       => $this->dn,
						'subjectPKInfo' => $publicKey, // @codingStandardsIgnoreLine.
					),
				'signatureAlgorithm'      => array( 'algorithm' => $signatureAlgorithm ), // @codingStandardsIgnoreLine.
				'signature'                => false, // this is going to be overwritten later .
			);
		}
		$certificationRequestInfo = $this->currentCert['certificationRequestInfo']; // @codingStandardsIgnoreLine.
		$this->loadCSR( $this->saveCSR( $this->currentCert ) ); // @codingStandardsIgnoreLine.
		$result                             = $this->_sign( $this->privateKey, $signatureAlgorithm ); // @codingStandardsIgnoreLine.
		$result['certificationRequestInfo'] = $certificationRequestInfo; // @codingStandardsIgnoreLine.
		$this->currentCert                  = $currentCert; // @codingStandardsIgnoreLine.
		$this->signatureSubject             = $signatureSubject; // @codingStandardsIgnoreLine.
		return $result;
	}

	/**
	 * Sign a SPKAC
	 *
	 * @access public
	 * @param string $signatureAlgorithm .
	 * @return mixed
	 */
	public function signSPKAC( $signatureAlgorithm = 'sha1WithRSAEncryption' ) { // @codingStandardsIgnoreLine.
		if ( ! is_object( $this->privateKey ) ) { // @codingStandardsIgnoreLine.
			return false;
		}
		$origPublicKey   = $this->publicKey; // @codingStandardsIgnoreLine.
		$class           = get_class( $this->privateKey ); // @codingStandardsIgnoreLine.
		$this->publicKey = new $class(); // @codingStandardsIgnoreLine.
		$this->publicKey->loadKey( $this->privateKey->getPublicKey() ); // @codingStandardsIgnoreLine.
		$this->publicKey->setPublicKey(); // @codingStandardsIgnoreLine.
		$publicKey = $this->_formatSubjectPublicKey(); // @codingStandardsIgnoreLine.
		if ( ! $publicKey ) { // @codingStandardsIgnoreLine.
			return false;
		}
		$this->publicKey  = $origPublicKey; // @codingStandardsIgnoreLine.
		$currentCert      = isset( $this->currentCert ) ? $this->currentCert : null; // @codingStandardsIgnoreLine.
		$signatureSubject = isset( $this->signatureSubject ) ? $this->signatureSubject : null; // @codingStandardsIgnoreLine.

		// re-signing a SPKAC seems silly but since everything else supports re-signing why not?
		if ( isset( $this->currentCert ) && is_array( $this->currentCert ) && isset( $this->currentCert['publicKeyAndChallenge'] ) ) { // @codingStandardsIgnoreLine.
			$this->currentCert['signatureAlgorithm']['algorithm'] = $signatureAlgorithm; // @codingStandardsIgnoreLine.
			$this->currentCert['publicKeyAndChallenge']['spki']   = $publicKey; // @codingStandardsIgnoreLine.
			if ( ! empty( $this->challenge ) ) {
				$this->currentCert['publicKeyAndChallenge']['challenge'] = $this->challenge & str_repeat( "\x7F", strlen( $this->challenge ) ); // @codingStandardsIgnoreLine.
			}
		} else {
			$this->currentCert = array( // @codingStandardsIgnoreLine.
				'publicKeyAndChallenge' =>
				array(
					'spki'      => $publicKey, // @codingStandardsIgnoreLine.
					'challenge' => ! empty( $this->challenge ) ? $this->challenge : '',
				),
				'signatureAlgorithm'         => array( 'algorithm' => $signatureAlgorithm ), // @codingStandardsIgnoreLine.
				'signature'             => false, // this is going to be overwritten later.
			);
		}
		// resync $this->signatureSubject
		// save $publicKeyAndChallenge in case there are any \phpseclib\File\ASN1\Element objects in it.
		$publicKeyAndChallenge = $this->currentCert['publicKeyAndChallenge']; // @codingStandardsIgnoreLine.
		$this->loadSPKAC( $this->saveSPKAC( $this->currentCert ) ); // @codingStandardsIgnoreLine.
		$result                          = $this->_sign( $this->privateKey, $signatureAlgorithm ); // @codingStandardsIgnoreLine.
		$result['publicKeyAndChallenge'] = $publicKeyAndChallenge; // @codingStandardsIgnoreLine.
		$this->currentCert               = $currentCert; // @codingStandardsIgnoreLine.
		$this->signatureSubject          = $signatureSubject; // @codingStandardsIgnoreLine.
		return $result;
	}

	/**
	 * Sign a CRL
	 *
	 * $issuer's private key needs to be loaded.
	 *
	 * @param \phpseclib\File\X509 $issuer .
	 * @param \phpseclib\File\X509 $crl .
	 * @param string               $signatureAlgorithm optional .
	 * @access public
	 * @return mixed
	 */
	public function signCRL( $issuer, $crl, $signatureAlgorithm = 'sha1WithRSAEncryption' ) { // @codingStandardsIgnoreLine.
		if ( ! is_object( $issuer->privateKey ) || empty( $issuer->dn ) ) { // @codingStandardsIgnoreLine.
				return false;
		}
		$currentCert      = isset( $this->currentCert ) ? $this->currentCert : null; // @codingStandardsIgnoreLine.
		$signatureSubject = isset( $this->signatureSubject ) ? $this->signatureSubject : null; // @codingStandardsIgnoreLine.
		$thisUpdate = ! empty( $this->startDate ) ? $this->startDate : @date( 'D, d M Y H:i:s O' ); // @codingStandardsIgnoreLine.
		if ( isset( $crl->currentCert ) && is_array( $crl->currentCert ) && isset( $crl->currentCert['tbsCertList'] ) ) { // @codingStandardsIgnoreLine.
			$this->currentCert = $crl->currentCert; // @codingStandardsIgnoreLine.
			$this->currentCert['tbsCertList']['signature']['algorithm'] = $signatureAlgorithm; // @codingStandardsIgnoreLine.
			$this->currentCert['signatureAlgorithm']['algorithm']       = $signatureAlgorithm; // @codingStandardsIgnoreLine.
		} else {
			$this->currentCert = array( // @codingStandardsIgnoreLine.
				'tbsCertList'        =>
					array(
						'version'    => 'v2',
						'signature'  => array( 'algorithm' => $signatureAlgorithm ), // @codingStandardsIgnoreLine.
						'issuer'     => false, // this is going to be overwritten later.
						'thisUpdate' => $this->_timeField( $thisUpdate ), // @codingStandardsIgnoreLine.
					),
				'signatureAlgorithm' => array( 'algorithm' => $signatureAlgorithm ), // @codingStandardsIgnoreLine.
				'signature'          => false, // this is going to be overwritten later.
			);
		}
		$tbsCertList               = &$this->currentCert['tbsCertList']; // @codingStandardsIgnoreLine.
		$tbsCertList['issuer']     = $issuer->dn; // @codingStandardsIgnoreLine.
		$tbsCertList['thisUpdate'] = $this->_timeField( $thisUpdate ); // @codingStandardsIgnoreLine.
		if ( ! empty( $this->endDate ) ) { // @codingStandardsIgnoreLine.
			$tbsCertList['nextUpdate'] = $this->_timeField( $this->endDate ); // @codingStandardsIgnoreLine.
		} else {
			unset( $tbsCertList['nextUpdate'] ); // @codingStandardsIgnoreLine.
		}
		if ( ! empty( $this->serialNumber ) ) { // @codingStandardsIgnoreLine.
			$crlNumber = $this->serialNumber; // @codingStandardsIgnoreLine.
		} else {
			$crlNumber = $this->getExtension( 'id-ce-cRLNumber' ); // @codingStandardsIgnoreLine.
			$crlNumber = $crlNumber !== false ? $crlNumber->add( new BigInteger( 1 ) ) : null; // @codingStandardsIgnoreLine.
		}
		$this->removeExtension( 'id-ce-authorityKeyIdentifier' );
		$this->removeExtension( 'id-ce-issuerAltName' );
		// Be sure version >= v2 if some extension found.
		$version = isset( $tbsCertList['version'] ) ? $tbsCertList['version'] : 0; // @codingStandardsIgnoreLine.
		if ( ! $version ) {
			if ( ! empty( $tbsCertList['crlExtensions'] ) ) { // @codingStandardsIgnoreLine.
				$version = 1; // v2.
			} elseif ( ! empty( $tbsCertList['revokedCertificates'] ) ) { // @codingStandardsIgnoreLine.
				foreach ( $tbsCertList['revokedCertificates'] as $cert ) { // @codingStandardsIgnoreLine.
					if ( ! empty( $cert['crlEntryExtensions'] ) ) {
						$version = 1; // v2.
					}
				}
			}
			if ( $version ) {
				$tbsCertList['version'] = $version; // @codingStandardsIgnoreLine.
			}
		}

		// Store additional extensions.
		if ( ! empty( $tbsCertList['version'] ) ) { // @codingStandardsIgnoreStart.
			if ( ! empty( $crlNumber ) ) {
				$this->setExtension( 'id-ce-cRLNumber', $crlNumber );
			}
			if ( isset( $issuer->currentKeyIdentifier ) ) {
				$this->setExtension('id-ce-authorityKeyIdentifier', array(
					'keyIdentifier' => $issuer->currentKeyIdentifier,
				));
			}
			$issuerAltName = $this->getExtension( 'id-ce-subjectAltName', $issuer->currentCert );
			if ( $issuerAltName !== false ) {
				$this->setExtension( 'id-ce-issuerAltName', $issuerAltName );
			}
		}
		if ( empty( $tbsCertList['revokedCertificates'] ) ) {
			unset( $tbsCertList['revokedCertificates'] );
		}
		unset( $tbsCertList );
		$tbsCertList = $this->currentCert['tbsCertList'];
		$this->loadCRL( $this->saveCRL( $this->currentCert ) );
		$result                 = $this->_sign( $issuer->privateKey, $signatureAlgorithm );
		$result['tbsCertList']  = $tbsCertList;
		$this->currentCert      = $currentCert;
		$this->signatureSubject = $signatureSubject;
		return $result;
	} // @codingStandardsIgnoreEnd.

	/**
	 * X.509 certificate signing helper function.
	 *
	 * @param object $key .
	 * @param string $signatureAlgorithm .
	 * @access public
	 * @return mixed
	 */
	public function _sign( $key, $signatureAlgorithm ) { // @codingStandardsIgnoreLine.
		if ( $key instanceof RSA ) {
			switch ( $signatureAlgorithm ) { // @codingStandardsIgnoreLine.
				case 'md2WithRSAEncryption':
				case 'md5WithRSAEncryption':
				case 'sha1WithRSAEncryption':
				case 'sha224WithRSAEncryption':
				case 'sha256WithRSAEncryption':
				case 'sha384WithRSAEncryption':
				case 'sha512WithRSAEncryption':
					$key->setHash( preg_replace( '#WithRSAEncryption$#', '', $signatureAlgorithm ) ); // @codingStandardsIgnoreLine.
					$key->setSignatureMode( RSA::SIGNATURE_PKCS1 );
					$this->currentCert['signature'] = base64_encode( "\0" . $key->sign( $this->signatureSubject ) ); // @codingStandardsIgnoreLine.
					return $this->currentCert; // @codingStandardsIgnoreLine.
			}
		}
		return false;
	}

	/**
	 * Set certificate start date
	 *
	 * @param string $date .
	 * @access public
	 */
	public function setStartDate( $date ) { // @codingStandardsIgnoreLine.
		$this->startDate = @date( 'D, d M Y H:i:s O', @strtotime( $date ) ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Set certificate end date
	 *
	 * @param string $date .
	 * @access public
	 */
	public function setEndDate( $date ) { // @codingStandardsIgnoreLine.
		if ( strtolower( $date ) == 'lifetime' ) { // WPCS:Loose comparison ok.
			$temp          = '99991231235959Z';
			$asn1          = new ASN1();
			$temp          = chr( ASN1::TYPE_GENERALIZED_TIME ) . $asn1->_encodeLength( strlen( $temp ) ) . $temp;
			$this->endDate = new Element( $temp ); // @codingStandardsIgnoreLine.
		} else {
			$this->endDate = @date( 'D, d M Y H:i:s O', @strtotime( $date ) ); // @codingStandardsIgnoreLine.
		}
	}

	/**
	 * Set Serial Number
	 *
	 * @param string $serial .
	 * @param string $base optional .
	 * @access public
	 */
	public function setSerialNumber( $serial, $base = -256 ) { // @codingStandardsIgnoreLine.
		$this->serialNumber = new BigInteger( $serial, $base ); // @codingStandardsIgnoreLine.
	}

	/**
	 * Turns the certificate into a certificate authority
	 *
	 * @access public
	 */
	public function makeCA() { // @codingStandardsIgnoreLine.
		$this->caFlag = true; // @codingStandardsIgnoreLine.
	}

	/**
	 * Check for validity of subarray
	 *
	 * This is intended for use in conjunction with _subArrayUnchecked(),
	 * implementing the checks included in _subArray() but without copying
	 * a potentially large array by passing its reference by-value to is_array().
	 *
	 * @param array  $root .
	 * @param string $path .
	 * @return boolean
	 * @access private
	 */
	private function _isSubArrayValid( $root, $path ) { // @codingStandardsIgnoreLine.
		if ( ! is_array( $root ) ) {
			return false;
		}
		foreach ( explode( '/', $path ) as $i ) {
			if ( ! is_array( $root ) ) {
				return false;
			}
			if ( ! isset( $root[ $i ] ) ) {
				return true;
			}
			$root = $root[ $i ];
		}
		return true;
	}

	/**
	 * Get a reference to a subarray
	 *
	 * This variant of _subArray() does no is_array() checking,
	 * so $root should be checked with _isSubArrayValid() first.
	 *
	 * This is here for performance reasons:
	 * Passing a reference (i.e. $root) by-value (i.e. to is_array())
	 * creates a copy. If $root is an especially large array, this is expensive.
	 *
	 * @param array  $root .
	 * @param string $path  absolute path with / as component separator .
	 * @param bool   $create optional .
	 * @access private
	 * @return array|false
	 */
	private function &_subArrayUnchecked( &$root, $path, $create = false ) { // @codingStandardsIgnoreLine.
		$false = false;
		foreach ( explode( '/', $path ) as $i ) {
			if ( ! isset( $root[ $i ] ) ) {
				if ( ! $create ) {
					return $false;
				}
				$root[ $i ] = array();
			}
			$root = &$root[ $i ];
		}
		return $root;
	}

	/**
	 * Get a reference to a subarray
	 *
	 * @param array  $root .
	 * @param string $path  absolute path with / as component separator .
	 * @param bool   $create optional .
	 * @access private
	 * @return array|false
	 */
	private function &_subArray( &$root, $path, $create = false ) { // @codingStandardsIgnoreLine.
		$false = false;
		if ( ! is_array( $root ) ) {
			return $false;
		}
		foreach ( explode( '/', $path ) as $i ) {
			if ( ! is_array( $root ) ) {
				return $false;
			}
			if ( ! isset( $root[ $i ] ) ) {
				if ( ! $create ) {
					return $false;
				}
				$root[ $i ] = array();
			}
			$root = &$root[ $i ];
		}
		return $root;
	}

	/**
	 * Get a reference to an extension subarray
	 *
	 * @param array  $root .
	 * @param string $path optional absolute path with / as component separator .
	 * @param bool   $create optional .
	 * @access private
	 * @return array|false
	 */
	private function &_extensions( &$root, $path = null, $create = false ) { // @codingStandardsIgnoreLine.
		if ( ! isset( $root ) ) {
			$root = $this->currentCert; // @codingStandardsIgnoreLine.
		}
		switch ( true ) {
			case ! empty( $path ):
			case ! is_array( $root ):
				break;
			case isset( $root['tbsCertificate'] ):
				$path = 'tbsCertificate/extensions';
				break;
			case isset( $root['tbsCertList'] ):
				$path = 'tbsCertList/crlExtensions';
				break;
			case isset( $root['certificationRequestInfo'] ):
				$pth        = 'certificationRequestInfo/attributes';
				$attributes = &$this->_subArray( $root, $pth, $create );
				if ( is_array( $attributes ) ) {
					foreach ( $attributes as $key => $value ) {
						if ( 'pkcs-9-at-extensionRequest' == $value['type'] ) { // WPCS:Loose comparison ok.
							$path = "$pth/$key/value/0";
							break 2;
						}
					}
					if ( $create ) {
						$key          = count( $attributes );
						$attributes[] = array(
							'type'  => 'pkcs-9-at-extensionRequest',
							'value' => array(),
						);
						$path         = "$pth/$key/value/0";
					}
				}
				break;
		}
		$extensions = &$this->_subArray( $root, $path, $create );
		if ( ! is_array( $extensions ) ) {
			$false = false;
			return $false;
		}
		return $extensions;
	}

	/**
	 * Remove an Extension
	 *
	 * @param string $id .
	 * @param string $path optional .
	 * @access private
	 * @return bool
	 */
	private function _removeExtension( $id, $path = null ) { // @codingStandardsIgnoreLine.
		$extensions = &$this->_extensions( $this->currentCert, $path ); // @codingStandardsIgnoreLine.
		if ( ! is_array( $extensions ) ) {
			return false;
		}
		$result = false;
		foreach ( $extensions as $key => $value ) {
			if ( $value['extnId'] == $id ) { // WPCS:Loose comparison ok.
				unset( $extensions[ $key ] );
				$result = true;
			}
		}
		$extensions = array_values( $extensions );
		return $result;
	}

	/**
	 * Get an Extension
	 *
	 * Returns the extension if it exists and false if not
	 *
	 * @param string $id .
	 * @param array  $cert optional .
	 * @param string $path optional .
	 * @access private
	 * @return mixed
	 */
	private function _getExtension( $id, $cert = null, $path = null ) { // @codingStandardsIgnoreLine.
		$extensions = $this->_extensions( $cert, $path );
		if ( ! is_array( $extensions ) ) {
			return false;
		}
		foreach ( $extensions as $key => $value ) {
			if ( $value['extnId'] == $id ) { // WPCS:Loose comparison ok.
				return $value['extnValue'];
			}
		}
		return false;
	}

	/**
	 * Returns a list of all extensions in use
	 *
	 * @param array  $cert optional .
	 * @param string $path optional .
	 * @access private
	 * @return array
	 */
	private function _getExtensions( $cert = null, $path = null ) { // @codingStandardsIgnoreLine.
		$exts       = $this->_extensions( $cert, $path );
		$extensions = array();
		if ( is_array( $exts ) ) {
			foreach ( $exts as $extension ) {
				$extensions[] = $extension['extnId'];
			}
		}
		return $extensions;
	}

	/**
	 * Set an Extension
	 *
	 * @param string $id .
	 * @param mixed  $value .
	 * @param bool   $critical optional .
	 * @param bool   $replace optional .
	 * @param string $path optional .
	 * @access private
	 * @return bool
	 */
	private function _setExtension( $id, $value, $critical = false, $replace = true, $path = null ) { // @codingStandardsIgnoreLine.
		$extensions = &$this->_extensions( $this->currentCert, $path, true ); // @codingStandardsIgnoreLine.
		if ( ! is_array( $extensions ) ) {
			return false;
		}
		$newext = array(
			'extnId'    => $id,
			'critical'  => $critical,
			'extnValue' => $value,
		);
		foreach ( $extensions as $key => $value ) {
			if ( $value['extnId'] == $id ) { // WPCS:Loose comparison ok.
				if ( ! $replace ) {
					return false;
				}
				$extensions[ $key ] = $newext;
				return true;
			}
		}
		$extensions[] = $newext;
		return true;
	}

	/**
	 * Remove a certificate, CSR or CRL Extension
	 *
	 * @param string $id .
	 * @access public
	 * @return bool
	 */
	public function removeExtension( $id ) { // @codingStandardsIgnoreLine.
		return $this->_removeExtension( $id );
	}

	/**
	 * Get a certificate, CSR or CRL Extension
	 *
	 * Returns the extension if it exists and false if not
	 *
	 * @param string $id .
	 * @param array  $cert optional .
	 * @access public
	 * @return mixed
	 */
	public function getExtension( $id, $cert = null ) { // @codingStandardsIgnoreLine.
		return $this->_getExtension( $id, $cert );
	}

	/**
	 * Returns a list of all extensions in use in certificate, CSR or CRL
	 *
	 * @param array $cert optional .
	 * @access public
	 * @return array
	 */
	public function getExtensions( $cert = null ) { // @codingStandardsIgnoreLine.
		return $this->_getExtensions( $cert );
	}

	/**
	 * Set a certificate, CSR or CRL Extension
	 *
	 * @param string $id .
	 * @param mixed  $value .
	 * @param bool   $critical optional .
	 * @param bool   $replace optional .
	 * @access public
	 * @return bool
	 */
	public function setExtension( $id, $value, $critical = false, $replace = true ) { // @codingStandardsIgnoreLine.
		return $this->_setExtension( $id, $value, $critical, $replace );
	}

	/**
	 * Remove a CSR attribute.
	 *
	 * @param string $id .
	 * @param int    $disposition optional .
	 * @access public
	 * @return bool
	 */
	public function removeAttribute( $id, $disposition = self::ATTR_ALL ) { // @codingStandardsIgnoreLine.
		$attributes = &$this->_subArray( $this->currentCert, 'certificationRequestInfo/attributes' ); // @codingStandardsIgnoreLine.
		if ( ! is_array( $attributes ) ) {
			return false;
		}
		$result = false;
		foreach ( $attributes as $key => $attribute ) {
			if ( $attribute['type'] == $id ) { // WPCS:Loose comparison ok.
				$n = count( $attribute['value'] );
				switch ( true ) {
					case self::ATTR_APPEND == $disposition: // WPCS:Loose comparison ok.
					case self::ATTR_REPLACE == $disposition: // WPCS:Loose comparison ok.
						return false;
					case $disposition >= $n:
						$disposition -= $n;
						break;
					case self::ATTR_ALL == $disposition: // WPCS:Loose comparison ok.
					case 1 == $n: // WPCS:Loose comparison ok.
						unset( $attributes[ $key ] );
						$result = true;
						break;
					default:
						unset( $attributes[ $key ]['value'][ $disposition ] );
						$attributes[ $key ]['value'] = array_values( $attributes[ $key ]['value'] );
						$result                      = true;
						break;
				}
				if ( self::ATTR_ALL != $result && $disposition ) { // WPCS:Loose comparison ok.
					break;
				}
			}
		}
		$attributes = array_values( $attributes );
		return $result;
	}

	/**
	 * Get a CSR attribute
	 *
	 * Returns the attribute if it exists and false if not
	 *
	 * @param string $id .
	 * @param int    $disposition optional .
	 * @param array  $csr optional .
	 * @access public
	 * @return mixed
	 */
	public function getAttribute( $id, $disposition = self::ATTR_ALL, $csr = null ) { // @codingStandardsIgnoreLine.
		if ( empty( $csr ) ) {
			$csr = $this->currentCert; // @codingStandardsIgnoreLine.
		}
		$attributes = $this->_subArray( $csr, 'certificationRequestInfo/attributes' );
		if ( ! is_array( $attributes ) ) {
			return false;
		}
		foreach ( $attributes as $key => $attribute ) {
			if ( $id == $attribute['type'] ) { // WPCS:Loose comparison ok.
				$n = count( $attribute['value'] );
				switch ( true ) {
					case $disposition == self::ATTR_APPEND: // @codingStandardsIgnoreLine.
					case $disposition == self::ATTR_REPLACE: // @codingStandardsIgnoreLine.
						return false;
					case $disposition == self::ATTR_ALL: // @codingStandardsIgnoreLine.
						return $attribute['value'];
					case $disposition >= $n:
						$disposition -= $n;
						break;
					default:
						return $attribute['value'][ $disposition ];
				}
			}
		}
		return false;
	}

	/**
	 * Returns a list of all CSR attributes in use
	 *
	 * @param array $csr optional .
	 * @access public
	 * @return array
	 */
	public function getAttributes( $csr = null ) { // @codingStandardsIgnoreLine.
		if ( empty( $csr ) ) {
			$csr = $this->currentCert; // @codingStandardsIgnoreLine.
		}
		$attributes = $this->_subArray( $csr, 'certificationRequestInfo/attributes' );
		$attrs      = array();
		if ( is_array( $attributes ) ) {
			foreach ( $attributes as $attribute ) {
				$attrs[] = $attribute['type'];
			}
		}
		return $attrs;
	}

	/**
	 * Set a CSR attribute
	 *
	 * @param string $id .
	 * @param mixed  $value .
	 * @param bool   $disposition optional .
	 * @access public
	 * @return bool
	 */
	public function setAttribute( $id, $value, $disposition = self::ATTR_ALL ) { // @codingStandardsIgnoreLine.
		$attributes = &$this->_subArray( $this->currentCert, 'certificationRequestInfo/attributes', true ); // @codingStandardsIgnoreLine.
		if ( ! is_array( $attributes ) ) {
			return false;
		}
		switch ( $disposition ) {
			case self::ATTR_REPLACE: // @codingStandardsIgnoreLine.
				$disposition = self::ATTR_APPEND;
			case self::ATTR_ALL:
				$this->removeAttribute( $id );
				break;
		}
		foreach ( $attributes as $key => $attribute ) {
			if ( $id == $attribute['type'] ) { // WPCS:Loose comparison ok.
				$n = count( $attribute['value'] );
				switch ( true ) {
					case $disposition == self::ATTR_APPEND: // @codingStandardsIgnoreLine.
						$last = $key;
						break;
					case $disposition >= $n:
						$disposition -= $n;
						break;
					default:
						$attributes[ $key ]['value'][ $disposition ] = $value;
						return true;
				}
			}
		}
		switch ( true ) {
			case $disposition >= 0:
				return false;
			case isset( $last ):
				$attributes[ $last ]['value'][] = $value;
				break;
			default:
				$attributes[] = array(
					'type'  => $id,
					'value' => self::ATTR_ALL == $disposition ? $value : array( $value ),
				); // WPCS:Loose comparison ok.
				break;
		}
		return true;
	}

	/**
	 * Sets the subject key identifier
	 *
	 * This is used by the id-ce-authorityKeyIdentifier and the id-ce-subjectKeyIdentifier extensions.
	 *
	 * @param string $value .
	 * @access public
	 */
	public function setKeyIdentifier( $value ) { // @codingStandardsIgnoreLine.
		if ( empty( $value ) ) {
			unset( $this->currentKeyIdentifier ); // @codingStandardsIgnoreLine.
		} else {
			$this->currentKeyIdentifier = base64_encode( $value ); // @codingStandardsIgnoreLine.
		}
	}

	/**
	 * Compute a public key identifier.
	 *
	 * Although key identifiers may be set to any unique value, this function
	 * computes key identifiers from public key according to the two
	 * recommended methods (4.2.1.2 RFC 3280).
	 * Highly polymorphic: try to accept all possible forms of key:
	 * - Key object
	 * - \phpseclib\File\X509 object with public or private key defined
	 * - Certificate or CSR array
	 * - \phpseclib\File\ASN1\Element object
	 * - PEM or DER string
	 *
	 * @param mixed $key optional .
	 * @param int   $method optional .
	 * @access public
	 * @return string binary key identifier
	 */
	public function computeKeyIdentifier( $key = null, $method = 1 ) { // @codingStandardsIgnoreLine.
		if ( is_null( $key ) ) {
			$key = $this;
		}
		switch ( true ) {
			case is_string( $key ):
				break;
			case is_array( $key ) && isset( $key['tbsCertificate']['subjectPublicKeyInfo']['subjectPublicKey'] ):
				return $this->computeKeyIdentifier( $key['tbsCertificate']['subjectPublicKeyInfo']['subjectPublicKey'], $method );
			case is_array( $key ) && isset( $key['certificationRequestInfo']['subjectPKInfo']['subjectPublicKey'] ):
				return $this->computeKeyIdentifier( $key['certificationRequestInfo']['subjectPKInfo']['subjectPublicKey'], $method );
			case ! is_object( $key ):
				return false;
			case $key instanceof Element:
				// Assume the element is a bitstring-packed key.
				$asn1    = new ASN1();
				$decoded = $asn1->decodeBER( $key->element );
				if ( empty( $decoded ) ) {
					return false;
				}
				$raw = $asn1->asn1map( $decoded[0], array( 'type' => ASN1::TYPE_BIT_STRING ) );
				if ( empty( $raw ) ) {
					return false;
				}
				$raw = base64_decode( $raw );
				// If the key is private, compute identifier from its corresponding public key.
				$key = new RSA();
				if ( ! $key->loadKey( $raw ) ) {
					return false;   // Not an unencrypted RSA key.
				}
				if ( $key->getPrivateKey() !== false ) {
					return $this->computeKeyIdentifier( $key, $method );
				}
				$key = $raw;    // Is a public key.
				break;
			case $key instanceof X509:
				if ( isset( $key->publicKey ) ) { // @codingStandardsIgnoreLine.
					return $this->computeKeyIdentifier( $key->publicKey, $method ); // @codingStandardsIgnoreLine.
				}
				if ( isset( $key->privateKey ) ) { // @codingStandardsIgnoreLine.
					return $this->computeKeyIdentifier( $key->privateKey, $method ); // @codingStandardsIgnoreLine.
				}
				if ( isset( $key->currentCert['tbsCertificate'] ) || isset( $key->currentCert['certificationRequestInfo'] ) ) { // @codingStandardsIgnoreLine.
					return $this->computeKeyIdentifier( $key->currentCert, $method ); // @codingStandardsIgnoreLine.
				}
				return false;
			default: // Should be a key object (i.e.: \phpseclib\Crypt\RSA).
				$key = $key->getPublicKey( RSA::PUBLIC_FORMAT_PKCS1 );
				break;
		}
		// If in PEM format, convert to binary.
		$key = $this->_extractBER( $key );
		// Now we have the key string: compute its sha-1 sum.
		$hash = new Hash( 'sha1' );
		$hash = $hash->hash( $key );
		if ( 2 == $method ) { // WPCS:Loose comparison ok.
			$hash    = substr( $hash, -8 );
			$hash[0] = chr( ( ord( $hash[0] ) & 0x0F ) | 0x40 );
		}
		return $hash;
	}

	/**
	 * Format a public key as appropriate
	 *
	 * @access private
	 * @return array
	 */
	private function _formatSubjectPublicKey() { // @codingStandardsIgnoreLine.
		if ( $this->publicKey instanceof RSA ) { // @codingStandardsIgnoreLine.
			return array(
				'algorithm'        => array( 'algorithm' => 'rsaEncryption' ),
				'subjectPublicKey' => $this->publicKey->getPublicKey( RSA::PUBLIC_FORMAT_PKCS1 ), // @codingStandardsIgnoreLine.
			);
		}
		return false;
	}

	/**
	 * Set the domain name's which the cert is to be valid for
	 *
	 * @access public
	 */
	public function setDomain() { // @codingStandardsIgnoreLine.
		$this->domains = func_get_args();
		$this->removeDNProp( 'id-at-commonName' );
		$this->setDNProp( 'id-at-commonName', $this->domains[0] );
	}

	/**
	 * Set the IP Addresses's which the cert is to be valid for
	 *
	 * @access public
	 */
	public function setIPAddress() { // @codingStandardsIgnoreLine.
		$this->ipAddresses = func_get_args(); // @codingStandardsIgnoreLine.
	}

	/**
	 * Helper function to build domain array
	 *
	 * @access private
	 * @param string $domain .
	 * @return array
	 */
	private function _dnsName( $domain ) { // @codingStandardsIgnoreLine.
		return array( 'dNSName' => $domain );
	}

	/**
	 * Helper function to build IP Address array
	 *
	 * (IPv6 is not currently supported)
	 *
	 * @access private
	 * @param string $address .
	 * @return array
	 */
	private function _iPAddress( $address ) { // @codingStandardsIgnoreLine.
		return array( 'iPAddress' => $address );
	}

	/**
	 * Get the index of a revoked certificate.
	 *
	 * @param array  $rclist .
	 * @param string $serial .
	 * @param bool   $create optional .
	 * @access private
	 * @return int|false
	 */
	private function _revokedCertificate( &$rclist, $serial, $create = false ) { // @codingStandardsIgnoreLine.
		$serial = new BigInteger( $serial );
		foreach ( $rclist as $i => $rc ) {
			if ( ! ( $serial->compare( $rc['userCertificate'] ) ) ) {
				return $i;
			}
		}
		if ( ! $create ) {
			return false;
		}
		$i        = count( $rclist );
		$rclist[] = array(
			'userCertificate' => $serial,
			'revocationDate'  => $this->_timeField( @date( 'D, d M Y H:i:s O' ) // @codingStandardsIgnoreLine.
			),
		);
		return $i;
	}

	/**
	 * Revoke a certificate.
	 *
	 * @param string $serial .
	 * @param string $date optional .
	 * @access public
	 * @return bool
	 */
	public function revoke( $serial, $date = null ) {
		if ( isset( $this->currentCert['tbsCertList'] ) ) { // @codingStandardsIgnoreLine.
			if ( is_array( $rclist = &$this->_subArray( $this->currentCert, 'tbsCertList/revokedCertificates', true ) ) ) { // @codingStandardsIgnoreLine.
				if ( $this->_revokedCertificate( $rclist, $serial ) === false ) { // If not yet revoked.
					if ( ( $i = $this->_revokedCertificate( $rclist, $serial, true ) ) !== false ) { // @codingStandardsIgnoreLine.
						if ( ! empty( $date ) ) {
							$rclist[ $i ]['revocationDate'] = $this->_timeField( $date );
						}
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Unrevoke a certificate.
	 *
	 * @param string $serial .
	 * @access public
	 * @return bool
	 */
	public function unrevoke( $serial ) {
		if ( is_array( $rclist = &$this->_subArray( $this->currentCert, 'tbsCertList/revokedCertificates' ) ) ) { // @codingStandardsIgnoreLine.
			if ( ( $i = $this->_revokedCertificate( $rclist, $serial ) ) !== false ) { // @codingStandardsIgnoreLine.
				unset( $rclist[ $i ] );
				$rclist = array_values( $rclist );
				return true;
			}
		}
		return false;
	}

	/**
	 * Get a revoked certificate.
	 *
	 * @param string $serial .
	 * @access public
	 * @return mixed
	 */
	public function getRevoked( $serial ) { // @codingStandardsIgnoreLine.
		if ( is_array( $rclist = $this->_subArray( $this->currentCert, 'tbsCertList/revokedCertificates' ) ) ) { // @codingStandardsIgnoreLine.
			if ( ( $i = $this->_revokedCertificate( $rclist, $serial ) ) !== false ) { // @codingStandardsIgnoreLine.
				return $rclist[ $i ];
			}
		}
		return false;
	}

	/**
	 * List revoked certificates
	 *
	 * @param array $crl optional .
	 * @access public
	 * @return array
	 */
	public function listRevoked( $crl = null ) { // @codingStandardsIgnoreLine.
		if ( ! isset( $crl ) ) {
			$crl = $this->currentCert; // @codingStandardsIgnoreLine.
		}
		if ( ! isset( $crl['tbsCertList'] ) ) {
			return false;
		}
		$result = array();
		if ( is_array( $rclist = $this->_subArray( $crl, 'tbsCertList/revokedCertificates' ) ) ) { // @codingStandardsIgnoreLine.
			foreach ( $rclist as $rc ) {
				$result[] = $rc['userCertificate']->toString();
			}
		}
		return $result;
	}

	/**
	 * Remove a Revoked Certificate Extension
	 *
	 * @param string $serial .
	 * @param string $id .
	 * @access public
	 * @return bool
	 */
	public function removeRevokedCertificateExtension( $serial, $id ) { // @codingStandardsIgnoreLine.
		if ( is_array( $rclist = &$this->_subArray( $this->currentCert, 'tbsCertList/revokedCertificates' ) ) ) { // @codingStandardsIgnoreLine.
			if ( ( $i = $this->_revokedCertificate( $rclist, $serial ) ) !== false ) { // @codingStandardsIgnoreLine.
				return $this->_removeExtension( $id, "tbsCertList/revokedCertificates/$i/crlEntryExtensions" );
			}
		}
		return false;
	}

	/**
	 * Get a Revoked Certificate Extension
	 *
	 * Returns the extension if it exists and false if not
	 *
	 * @param string $serial .
	 * @param string $id .
	 * @param array  $crl optional .
	 * @access public
	 * @return mixed
	 */
	public function getRevokedCertificateExtension( $serial, $id, $crl = null ) { // @codingStandardsIgnoreLine.
		if ( ! isset( $crl ) ) {
			$crl = $this->currentCert; // @codingStandardsIgnoreLine.
		}
		if ( is_array( $rclist = $this->_subArray( $crl, 'tbsCertList/revokedCertificates' ) ) ) { // @codingStandardsIgnoreLine.
			if ( ( $i = $this->_revokedCertificate( $rclist, $serial ) ) !== false ) { // @codingStandardsIgnoreLine.
				return $this->_getExtension( $id, $crl, "tbsCertList/revokedCertificates/$i/crlEntryExtensions" );
			}
		}
		return false;
	}

	/**
	 * Returns a list of all extensions in use for a given revoked certificate
	 *
	 * @param string $serial .
	 * @param array  $crl optional .
	 * @access public
	 * @return array
	 */
	public function getRevokedCertificateExtensions( $serial, $crl = null ) { // @codingStandardsIgnoreLine.
		if ( ! isset( $crl ) ) {
			$crl = $this->currentCert; // @codingStandardsIgnoreLine.
		}
		if ( is_array( $rclist = $this->_subArray( $crl, 'tbsCertList/revokedCertificates' ) ) ) { // @codingStandardsIgnoreLine.
			if ( ( $i = $this->_revokedCertificate( $rclist, $serial ) ) !== false ) { // @codingStandardsIgnoreLine.
				return $this->_getExtensions( $crl, "tbsCertList/revokedCertificates/$i/crlEntryExtensions" );
			}
		}
		return false;
	}

	/**
	 * Set a Revoked Certificate Extension
	 *
	 * @param string $serial .
	 * @param string $id .
	 * @param mixed  $value .
	 * @param bool   $critical optional .
	 * @param bool   $replace optional .
	 * @access public
	 * @return bool
	 */
	public function setRevokedCertificateExtension( $serial, $id, $value, $critical = false, $replace = true ) { // @codingStandardsIgnoreLine.
		if ( isset( $this->currentCert['tbsCertList'] ) ) { // @codingStandardsIgnoreLine.
			if ( is_array( $rclist = &$this->_subArray( $this->currentCert, 'tbsCertList/revokedCertificates', true ) ) ) { // @codingStandardsIgnoreLine.
				if ( ( $i = $this->_revokedCertificate( $rclist, $serial, true ) ) !== false ) { // @codingStandardsIgnoreLine.
					return $this->_setExtension( $id, $value, $critical, $replace, "tbsCertList/revokedCertificates/$i/crlEntryExtensions" );
				}
			}
		}
			return false;
	}

	/**
	 * Extract raw BER from Base64 encoding
	 *
	 * @access private
	 * @param string $str .
	 * @return string
	 */
	private function _extractBER( $str ) { // @codingStandardsIgnoreLine.
		$temp = preg_replace( '#.*?^-+[^-]+-+[\r\n ]*$#ms', '', $str, 1 );
		// remove the -----BEGIN CERTIFICATE----- and -----END CERTIFICATE----- stuff .
		$temp = preg_replace( '#-+[^-]+-+#', '', $temp );
		// remove new lines .
		$temp = str_replace( array( "\r", "\n", ' ' ), '', $temp );
		$temp = preg_match( '#^[a-zA-Z\d/+]*={0,2}$#', $temp ) ? base64_decode( $temp ) : false;
		return false != $temp ? $temp : $str; // WPCS:Loose comparison ok.
	}

	/**
	 * Returns the OID corresponding to a name
	 *
	 * What's returned in the associative array returned by loadX509() (or load*()) is either a name or an OID if
	 * no OID to name mapping is available. The problem with this is that what may be an unmapped OID in one version
	 * of phpseclib may not be unmapped in the next version, so apps that are looking at this OID may not be able
	 * to work from version to version.
	 *
	 * This method will return the OID if a name is passed to it and if no mapping is avialable it'll assume that
	 * what's being passed to it already is an OID and return that instead. A few examples.
	 *
	 * getOID('2.16.840.1.101.3.4.2.1') == '2.16.840.1.101.3.4.2.1'
	 * getOID('id-sha256') == '2.16.840.1.101.3.4.2.1'
	 * getOID('zzz') == 'zzz'
	 *
	 * @param string $name .
	 * @access public
	 * @return string
	 */
	public function getOID( $name ) { // @codingStandardsIgnoreLine.
		static $reverseMap; // @codingStandardsIgnoreLine.
		if ( ! isset( $reverseMap ) ) { // @codingStandardsIgnoreLine.
			$reverseMap = array_flip( $this->oids ); // @codingStandardsIgnoreLine.
		}
		return isset( $reverseMap[ $name ] ) ? $reverseMap[ $name ] : $name; // @codingStandardsIgnoreLine.
	}
}
