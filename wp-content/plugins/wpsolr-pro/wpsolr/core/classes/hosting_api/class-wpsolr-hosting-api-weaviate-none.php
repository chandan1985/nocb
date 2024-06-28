<?php

namespace wpsolr\core\classes\hosting_api;

use wpsolr\core\classes\engines\WPSOLR_AbstractEngineClient;

class WPSOLR_Hosting_Api_Weaviate_None extends WPSOLR_Hosting_Api_Abstract {

	const HOSTING_API_ID = 'none_weaviate';

	/**
	 * @inerhitDoc
	 */
	public function get_is_disabled() {
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function get_is_no_hosting() {
		return true;
	}

	/**
	 * @inerhitDoc
	 */
	public function get_is_endpoint_only() {
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function get_label() {
		return sprintf( self::NONE_LABEL, 'Weaviate' );
	}

	/**
	 * @inheritdoc
	 */
	public function get_search_engine() {
		return WPSOLR_AbstractEngineClient::ENGINE_WEAVIATE;
	}

	/**
	 * @inheritDoc
	 */
	public function get_is_host_contains_user_password() {
		return false; // Weaviate does not require it
	}

	/**
	 * @return string
	 */
	public function get_documentation_url() {
		return 'https://www.wpsolr.com/guide/configuration-step-by-step-schematic/configure-your-indexes/create-weaviate-index/';
	}

	/**
	 * @inheritdoc
	 */
	public function get_ui_fields_child() {

		$result = [
			static::FIELD_NAME_FIELDS_INDEX_LABEL_DEFAULT,
			[
				self::FIELD_NAME_FIELDS_INDEX_ENDPOINT => [
					self::FIELD_NAME_LABEL                 => 'Server URL',
					self::FIELD_NAME_PLACEHOLDER           => 'Copy a Weaviate instance URL here, like http://localhost:8080',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your Server URL here',
					],
				],
			],

			[
				self::FIELD_NAME_FIELDS_INDEX_API_KEY => [
					self::FIELD_NAME_LABEL                 => 'API key',
					self::FIELD_NAME_PLACEHOLDER           => 'Your API key',
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'your API key',
					],
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
				]
			],

			/**
			 * OpenAI API
			 */
			[
				self::FIELD_NAME_FIELDS_INDEX_WEAVIATE_OPENAI_CONFIG_TYPE => [
					self::FIELD_NAME_LABEL                 => 'OpenAI vectorizer type',
					self::FIELD_NAME_PLACEHOLDER           => 'Choose a type among: text, code',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your OpenAI vectorizer type here',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_WEAVIATE_OPENAI_CONFIG_MODEL => [
					self::FIELD_NAME_LABEL                 => 'OpenAI vectorizer model',
					self::FIELD_NAME_PLACEHOLDER           => 'For document embeddings choose among: ada, babbage, curie, davinci. For code embeddings choose among: ada, babbage',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your OpenAI vectorizer model here',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_WEAVIATE_OPENAI_CONFIG_MODEL_VERSION => [
					self::FIELD_NAME_LABEL                 => 'OpenAI vectorizer model version',
					self::FIELD_NAME_PLACEHOLDER           => '001, 002, ...',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your OpenAI vectorizer model version here',
					],
				],
			],

			/**
			 * QnA OpenAI API
			 */
			[
				self::FIELD_NAME_FIELDS_INDEX_WEAVIATE_OPENAI_CONFIG_TYPE_QNA => [
					self::FIELD_NAME_LABEL                 => 'OpenAI QnA type',
					self::FIELD_NAME_PLACEHOLDER           => 'Choose a type among: text, code',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your OpenAI QnA type here',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_WEAVIATE_OPENAI_CONFIG_MODEL_QNA => [
					self::FIELD_NAME_LABEL                 => 'OpenAI QnA model',
					self::FIELD_NAME_PLACEHOLDER           => 'For document embeddings choose among: ada, babbage, curie, davinci. For code embeddings choose among: ada, babbage',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your OpenAI QnA model here',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_WEAVIATE_OPENAI_CONFIG_MODEL_VERSION_QNA => [
					self::FIELD_NAME_LABEL                 => 'OpenAI QnA model version',
					self::FIELD_NAME_PLACEHOLDER           => '001, 002, ...',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your OpenAI QnA model version here',
					],
				],
			],

			/**
			 * HuggingFace API
			 */
			[
				self::FIELD_NAME_FIELDS_INDEX_WEAVIATE_HUGGINGFACE_CONFIG_MODEL => [
					self::FIELD_NAME_LABEL                 => 'Hugging Face model, or DPR passage model, or Inference Endpoint URL',
					self::FIELD_NAME_PLACEHOLDER           => 'This can be any public or private Hugging Face model, sentence similarity models work best for vectorization.',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your Hugging Face model here',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_WEAVIATE_HUGGINGFACE_CONFIG_MODEL_QUERY => [
					self::FIELD_NAME_LABEL                 => 'Hugging Face DPR query model',
					self::FIELD_NAME_PLACEHOLDER           => 'Should be set together with a DPR passage model. Empty if the model is not a DPR passage model.',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_NOT_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your Hugging Face DPR query model here',
					],
				],
			],

			/**
			 * Cohere API
			 */
			[
				self::FIELD_NAME_FIELDS_INDEX_WEAVIATE_COHERE_CONFIG_MODEL => [
					self::FIELD_NAME_LABEL                 => 'Cohere model',
					self::FIELD_NAME_PLACEHOLDER           => 'multilingual-22-12',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => true,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your Cohere model here',
					],
				],
			],

			/**
			 * Google PaLM API
			 */
			[
				self::FIELD_NAME_FIELDS_INDEX_KEY_JSON => [
					self::FIELD_NAME_LABEL                 => 'Service account JSON key of the Google Project you authorized the Vertex Palm API',
					self::FIELD_NAME_PLACEHOLDER           => 'Service account JSON key',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_ENTER . 'your service account JSON key',
					],
				],
			],
			[
				self::FIELD_NAME_FIELDS_INDEX_WEAVIATE_GOOGLE_PALM_CONFIG_MODEL => [
					self::FIELD_NAME_LABEL                 => '<a href="https://weaviate.io/developers/weaviate/modules/retriever-vectorizer-modules/text2vec-palm#available-model" target="_new">Google PaLM model</a>',
					self::FIELD_NAME_PLACEHOLDER           => 'textembedding-gecko',
					self::FIELD_NAME_FORMAT_IS_CREATE_ONLY => false,
					self::FIELD_NAME_DEFAULT_VALUE         => 'textembedding-gecko',
					self::FIELD_NAME_FORMAT                => [
						self::FIELD_NAME_FORMAT_TYPE        => self::FIELD_NAME_FORMAT_TYPE_MANDATORY,
						self::FIELD_NAME_FORMAT_ERROR_LABEL => self::PLEASE_COPY . 'your Google PaLM model here',
					],
				],
			],
			self::FIELD_NAME_FIELDS_INDEX_TOKEN_DEFAULT,

		];

		return $result;
	}

}