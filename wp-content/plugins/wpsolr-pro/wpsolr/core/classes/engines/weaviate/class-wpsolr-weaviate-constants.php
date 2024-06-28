<?php

namespace wpsolr\core\classes\engines\weaviate;

class WPSOLR_Weaviate_Constants {

	const MODULE_TEXT_2_VEC_PALM = 'text2vec-palm';
	const MODULE_TEXT_2_VEC_COHERE = 'text2vec-cohere';
	const MODULE_TEXT_2_VEC_HUGGINGFACE = 'text2vec-huggingface';
	const MODULE_TEXT_2_VEC_OPENAI = 'text2vec-openai';
	const MODULE_QNA_OPENAI = 'qna-openai';
	const MODULE_TEXT_2_VEC_CONTEXTIONARY = 'text2vec-contextionary';

	const MODULE_MULTI2VEC_CLIP = 'multi2vec-clip';
	const MODULE_NONE = 'none';
	const MODULE_TEXT_2_VEC_TRANSFORMERS = 'text2vec-transformers';
	const MODULE_IMG2VEC_NEURAL = 'img2vec-neural';

	const MODULE_NER_TRANSFORMERS = 'ner-transformers';
	const MODULE_QNA_TRANSFORMERS = 'qna-transformers';
	const MODULE_TEXT_SPELLCHECK = 'text-spellcheck';
}