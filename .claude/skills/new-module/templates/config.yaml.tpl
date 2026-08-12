# ---------------------------------------------------------------------------
# config/services/{{module}}.yaml
# ---------------------------------------------------------------------------
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\{{Module}}\:
        resource: '../../src/{{Module}}/*'

# ---------------------------------------------------------------------------
# config/routes/{{module}}.yaml
#
# Website routes take a locale MAP, never a plain string prefix — a string
# makes the route English-only and breaks the hreflang alternates emitted by
# templates/app/base.html.twig. Dashboard routes stay locale-agnostic.
# ---------------------------------------------------------------------------
website_{{module}}_controllers:
    resource:
        path: ../../src/{{Module}}/Ui/Controller/Website
        namespace: App\{{Module}}\Ui\Controller\Website
    type: attribute
    prefix:
        en: '/{{module}}'
        fr: '/fr/{{module}}'
    name_prefix: app_website_{{module}}_
    trailing_slash_on_root: false

dashboard_{{module}}_controllers:
    resource:
        path: ../../src/{{Module}}/Ui/Controller/Dashboard
        namespace: App\{{Module}}\Ui\Controller\Dashboard
    type: attribute
    prefix: '/dashboard/{{module}}'
    name_prefix: app_dashboard_{{module}}_
    trailing_slash_on_root: false
