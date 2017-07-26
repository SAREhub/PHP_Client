master_doc = 'index'

project = u'SAREhub'
copyright = u'2016, SARE SA'
author = u'SARE SA'

version = "0.4.7"
release = "0.4.7"
html_logo = 'assets/img/logo.svg'
html_favicon = 'assets/img/favicon.ico'
html_show_sphinx = False
html_static_path = [
'assets/'
]

html_context = {
 'css_files': [
  'https://media.readthedocs.org/css/sphinx_rtd_theme.css',
  'https://media.readthedocs.org/css/readthedocs-doc-embed.css',
  '_static/css/extra.css',
 ]
}

latex_logo = 'assets/img/logo.png'
latex_documents = [
  (master_doc, 'index.tex', u'SAREhub',
   u'SARE SA', 'manual', True),
]
