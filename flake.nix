{
  description = "Development environment for OrganizeAi project on macOS (Apple Silicon)";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-24.05";
  };

  outputs = { self, nixpkgs }:
  let
    system = "aarch64-darwin";
    pkgs = import nixpkgs { inherit system; };
    gdk = pkgs.google-cloud-sdk.withExtraComponents( with pkgs.google-cloud-sdk.components; [
        gke-gcloud-auth-plugin
    ]);
  in {
    devShells.${system} = {
      default = pkgs.mkShell {
        buildInputs = with pkgs; [
          fish
          docker
          docker-compose
          php
          nodejs
          yarn
          postgresql
          gnumake
          # redis
        ];

        shellHook = ''
            export PATH="$PATH:$HOME/.composer/vendor/bin"
            source .env.gcloud
            gcloud auth activate-service-account --key-file=service-account.json
            gcloud config set project $PROJECT_ID
            echo "Welcome to the development environment"
        '';
      };
    };
  };
}
