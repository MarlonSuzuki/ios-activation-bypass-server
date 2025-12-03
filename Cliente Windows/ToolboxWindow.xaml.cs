using System;
using System.Windows;

namespace ClienteWindows
{
    public partial class ToolboxWindow : Window
    {
        public enum ToolboxAction
        {
            None,
            Reboot,
            Erase,
            DevMode,
            HelloNoDeactivate,
            HelloDeactivate
        }

        public ToolboxAction SelectedAction { get; private set; } = ToolboxAction.None;

        public ToolboxWindow()
        {
            InitializeComponent();
        }

        private void Reboot_Click(object sender, RoutedEventArgs e)
        {
            SelectedAction = ToolboxAction.Reboot;
            this.DialogResult = true;
            this.Close();
        }

        private void Erase_Click(object sender, RoutedEventArgs e)
        {
            var confirm = MessageBox.Show(
                "ATENÇÃO: Isso vai apagar TODOS os dados!\n\nTem certeza?",
                "Erase Device - Confirmação",
                MessageBoxButton.YesNo,
                MessageBoxImage.Warning);

            if (confirm == MessageBoxResult.Yes)
            {
                SelectedAction = ToolboxAction.Erase;
                this.DialogResult = true;
                this.Close();
            }
        }

        private void DevMode_Click(object sender, RoutedEventArgs e)
        {
            SelectedAction = ToolboxAction.DevMode;
            this.DialogResult = true;
            this.Close();
        }

        private void HelloNoDeactivate_Click(object sender, RoutedEventArgs e)
        {
            var confirm = MessageBox.Show(
                "Isso vai fazer o iPhone retornar à tela Hello\nsem desativar o dispositivo.\n\nContinuar?",
                "Return to Hello",
                MessageBoxButton.YesNo,
                MessageBoxImage.Question);

            if (confirm == MessageBoxResult.Yes)
            {
                SelectedAction = ToolboxAction.HelloNoDeactivate;
                this.DialogResult = true;
                this.Close();
            }
        }

        private void HelloDeactivate_Click(object sender, RoutedEventArgs e)
        {
            var confirm = MessageBox.Show(
                "Isso vai fazer o iPhone retornar à tela Hello\nE DESATIVAR o dispositivo.\n\nTem certeza?",
                "Return to Hello (Deactivating)",
                MessageBoxButton.YesNo,
                MessageBoxImage.Warning);

            if (confirm == MessageBoxResult.Yes)
            {
                SelectedAction = ToolboxAction.HelloDeactivate;
                this.DialogResult = true;
                this.Close();
            }
        }

        private void Cancel_Click(object sender, RoutedEventArgs e)
        {
            SelectedAction = ToolboxAction.None;
            this.DialogResult = false;
            this.Close();
        }
    }
}
