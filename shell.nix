{pkgs ? import <nixpkgs> {}}:

let
  gdk = pkgs.google-cloud-sdk.withExtraComponents( with pkgs.google-cloud-sdk.components; [
    gke-gcloud-auth-plugin
  ]);
in

pkgs.mkShell {

    packages = [
        pkgs.php83
        gdk
    ];

    shellHook = "
        echo 'Setting up gcloud'
        source .env
        source .env.gcloudvars
    ";

    inputsFrom = [ pkgs.bat ];

}
