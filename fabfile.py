from tools.fablib import *

from fabric.api import task

"""
Base configuration
"""
env.project_name = 'mjwp'
env.hosts = ['localhost', ]
env.domain = 'mjwp.dev'

# Environments
@task
def production():
    """
    Work on production environment
    """
    env.settings    = 'production'
    env.hosts       = [ os.environ[ 'MJWP_PRODUCTION_SFTP_HOST' ], ]   # ssh host for production.
    env.user        = os.environ[ 'MJWP_PRODUCTION_SFTP_USER' ]        # ssh user for production.
    env.password    = os.environ[ 'MJWP_PRODUCTION_SFTP_PASSWORD' ]    # ssh password for production.
    env.domain      = 'dev-mjwordpress.pantheonsite.io'
    env.port        = '2222'

#@task
#def staging():
#    """
#    Work on staging environment
#    """
#    env.settings    = 'staging'
#    env.user        = os.environ[ 'MJWP_STAGING_SFTP_USER' ],       # ssh user for production.
#    env.password    = os.environ[ 'MJWP_STAGING_SFTP_PASSWORD' ]    # ssh password for production.
#    env.domain      = ''
#    env.port        = '2222'

try:
    from local_fabfile import  *
except ImportError:
    pass
