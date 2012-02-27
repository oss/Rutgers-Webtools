%define name webtools_mailman 
%define version 1.0
%define release 5.ru
%define prefix /usr/local

Summary: Web application allowing users to configure Mailman.
Name: %name
Version: %version
Release: %release
License: GPL
Group: Services
Source0: %{name}-%{version}.tgz 
BuildRoot: %{_tmppath}/%{name}-root

%description
This is an addon package to webtools. These tools are an addon to assist Mailman's lack of user tools to do things such as request a new password, list the members of a particular list, look at the configuration of a particular list, etc.


%prep
%setup -n %{name}-%{version}

%build

%install

PATH="/usr/local/gnu/bin:$PATH"
export PATH

rm -rf $RPM_BUILD_ROOT
mkdir -p 0755 -p $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}
mkdir -p 0755 $RPM_BUILD_ROOT/usr/%{_lib}/webtools/config
mkdir -p 0755 $RPM_BUILD_ROOT/usr/%{_lib}/webtools/html/mailman

install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/README $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}/

install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/config/* $RPM_BUILD_ROOT%{_libdir}/webtools/config/
#install -c -m 0644 $RPM_BUILD_DIR/%{name}-%{version}/html/mailman/* $RPM_BUILD_ROOT%{_libdir}/webtools/html/mailman/
cp -R -a $RPM_BUILD_DIR/%{name}-%{version}/html/mailman/* $RPM_BUILD_ROOT%{_libdir}/webtools/html/mailman/
cd $RPM_BUILD_ROOT%{_libdir}/webtools/html/mailman/

%post
echo "README is located at /usr/share/doc/%{name}-%{version}/README";
echo "Do the following:";
echo "chmod 555 %{_libdir}/mailman/bin/change_pw";
echo "chmod 555 %{_libdir}/mailman/bin/config_list";
echo "chmod 555 %{_libdir}/mailman/bin/dumpdb";
echo "chmod 555 %{_libdir}/mailman/bin/find_member";
echo "chmod 555 %{_libdir}/mailman/bin/list_lists";
echo "chmod 555 %{_libdir}/mailman/bin/list_members";
echo "chmod 555 %{_libdir}/mailman/bin/remove_members";
echo "ln -s %{_libdir}/mailman/bin/change_pw %{_libdir}/webtools/webbin/change_pw";
echo "ln -s %{_libdir}/mailman/bin/config_list %{_libdir}/webtools/webbin/config_list";
echo "ln -s %{_libdir}/mailman/bin/dumpdb %{_libdir}/webtools/webbin/dumpdb";
echo "ln -s %{_libdir}/mailman/bin/find_member %{_libdir}/webtools/webbin/find_member";
echo "ln -s %{_libdir}/mailman/bin/list_lists %{_libdir}/webtools/webbin/list_lists";
echo "ln -s %{_libdir}/mailman/bin/list_members %{_libdir}/webtools/webbin/list_members";
echo "ln -s %{_libdir}/mailman/bin/remove_members %{_libdir}/webtools/webbin/remove_members";

%files
/usr/%{_lib}/webtools/config/*
/usr/share/doc/%{name}-%{version}/*
/usr/%{_lib}/webtools/html/mailman/*

%changelog
* Mon Feb 15 2010 Orcan Ogetbil <orcan@nbcs.rutgers.edu> 1.0-5.ru
- Software changes:
  o Updated auditlists webtool: removed the need for find/cat/statfile
    programs via runas, used php equivalent functions.

* Mon Nov 16 2009 Orcan Ogetbil <orcan@nbcs.rutgers.edu> 1.0-4.ru
- Software changes:
  o Updated support/listmembers/myfunctions.php to handle lists with
    large numbers of members.

* Mon Oct 19 2009 Orcan Ogetbil <orcan@@nbcs.rutgers.edu> 1.0-3.ru
- Change "/usr/lib64/mailman" to "%%{_libdir}/mailman" in postinstall echo

* Mon Oct 19 2009 Orcan Ogetbil <orcan@@nbcs.rutgers.edu> 1.0-2.ru
- Change "/usr/lib/mailman" to "/usr/lib64/mailman" in postinstall echo
- Software changes:
  o Alter path of relative links (e.g., /home to /mailman) in many webtools files
  o Cleaned up myconfig.php files and renamed them to myconfig.php.sample
  o The newlist webtool fixed for undefined indexes, and new config variables introduced
  o README updated 

* Wed Oct 14 2009 Naveen Gavini <ngavini@nbcs.rutgers.edu> 1.0-1.ru
- Initial Build.
